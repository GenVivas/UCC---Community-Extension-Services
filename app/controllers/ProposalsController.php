<?php

class ProposalsController extends Controller
{
    private ProposalModel $model;

    public function __construct()
    {
        $this->model = new ProposalModel();
    }

    public function index(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('proposals.view');

        $page    = max(1, (int)$this->query('page', 1));
        $filters = [
            'search'     => $this->query('search', ''),
            'status'     => $this->query('status', ''),
            'program_id' => $this->query('program_id', ''),
        ];

        $paginator = $this->model->getAllPaginated($page, $filters);
        $programs  = $this->model->getPrograms();

        $this->view('proposals/index', [
            'pageTitle' => 'Project Proposals',
            'paginator' => $paginator,
            'filters'   => $filters,
            'programs'  => $programs,
        ]);
    }

    public function create(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('proposals.create');

        $this->view('proposals/create', [
            'pageTitle' => 'New Proposal',
            'programs'  => $this->model->getPrograms(),
            'barangays' => $this->model->getBarangays(),
            'cnas'      => $this->model->getApprovedCNAs(),
            'cnaId'     => $this->query('cna_id', ''),
        ]);
    }

    public function store(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('proposals.create');
        $this->validateCsrf();

        $attachment = null;
        if (!empty($_FILES['attachment']['name'])) {
            $attachment = uploadFile($_FILES['attachment'], UPLOAD_PROPOSALS, ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
            if (!$attachment) {
                Session::flash('error', 'Invalid attachment. Only PDF/DOC allowed (max 5MB).');
                $this->redirect('proposals/create');
            }
        }

        $data = [
            ':assessment_id'        => $this->input('assessment_id') ?: null,
            ':barangay_id'          => $this->input('barangay_id'),
            ':program_id'           => $this->input('program_id'),
            ':submitted_by'         => Auth::id(),
            ':title'                => $this->input('title'),
            ':description'          => $this->input('description'),
            ':objectives'           => $this->input('objectives'),
            ':target_area'          => $this->input('target_area'),
            ':target_beneficiaries' => $this->input('target_beneficiaries'),
            ':expected_output'      => $this->input('expected_output'),
            ':start_date'           => $this->input('start_date') ?: null,
            ':end_date'             => $this->input('end_date')   ?: null,
            ':budget_requested'     => $this->input('budget_requested', 0),
            ':attachment'           => $attachment,
            ':status'               => $this->input('action') === 'submit' ? 'pending' : 'draft',
        ];

        $v = Validator::make($_POST)
            ->required('barangay_id', 'Barangay')
            ->required('program_id',  'Program')
            ->required('title',       'Title')
            ->required('target_area', 'Target Area');

        if ($v->fails()) {
            foreach ($v->allErrors() as $err) Session::flash('error', $err);
            $this->redirect('proposals/create');
        }

        $id = $this->model->create($data);
        auditLog('create_proposal', 'proposals', (int)$id, "Created: {$data[':title']}");
        Session::flash('success', 'Proposal saved successfully.');
        $this->redirect('proposals/view/' . $id);
    }

    public function view(string $id): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('proposals.view');

        $proposal = $this->model->findById((int)$id);
        if (!$proposal) $this->abort(404, 'Proposal not found.');

        $activities = $this->model->getActivities((int)$id);
        $history    = $this->model->getApprovalHistory((int)$id);

        $this->view('proposals/view', [
            'pageTitle'  => 'View Proposal',
            'proposal'   => $proposal,
            'activities' => $activities,
            'history'    => $history,
        ]);
    }

    public function edit(string $id): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('proposals.edit');

        $proposal = $this->model->findById((int)$id);
        if (!$proposal) $this->abort(404, 'Proposal not found.');
        if (in_array($proposal['status'], ['approved','ongoing','completed'])) {
            Session::flash('error', 'This proposal cannot be edited in its current status.');
            $this->redirect('proposals/view/' . $id);
        }

        $this->view('proposals/edit', [
            'pageTitle' => 'Edit Proposal',
            'proposal'  => $proposal,
            'programs'  => $this->model->getPrograms(),
            'barangays' => $this->model->getBarangays(),
            'cnas'      => $this->model->getApprovedCNAs(),
        ]);
    }

    public function update(string $id): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('proposals.edit');
        $this->validateCsrf();

        $data = [
            ':title'                => $this->input('title'),
            ':description'          => $this->input('description'),
            ':objectives'           => $this->input('objectives'),
            ':target_area'          => $this->input('target_area'),
            ':target_beneficiaries' => $this->input('target_beneficiaries'),
            ':expected_output'      => $this->input('expected_output'),
            ':start_date'           => $this->input('start_date') ?: null,
            ':end_date'             => $this->input('end_date')   ?: null,
            ':budget_requested'     => $this->input('budget_requested', 0),
            ':barangay_id'          => $this->input('barangay_id'),
            ':program_id'           => $this->input('program_id'),
            ':status'               => $this->input('action') === 'submit' ? 'pending' : 'draft',
        ];

        $this->model->update((int)$id, $data);
        auditLog('update_proposal', 'proposals', (int)$id);
        Session::flash('success', 'Proposal updated.');
        $this->redirect('proposals/view/' . $id);
    }

    public function approve(string $id): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('proposals.approve');
        $this->validateCsrf();

        $action  = $this->input('action');   // 'approved' | 'rejected' | 'returned'
        $remarks = $this->input('remarks');

        if ($action === 'approved') {
            $this->model->approve((int)$id, Auth::id());
        } elseif ($action === 'rejected') {
            $this->model->updateStatus((int)$id, 'rejected', $remarks);
        } else {
            $this->model->updateStatus((int)$id, 'draft');
        }

        $this->model->logApproval([
            ':proposal_id'   => $id,
            ':approver_id'   => Auth::id(),
            ':approver_role' => Auth::role(),
            ':action'        => $action,
            ':remarks'       => $remarks,
        ]);

        auditLog('proposal_' . $action, 'proposals', (int)$id);
        Session::flash('success', 'Proposal ' . $action . '.');
        $this->redirect('proposals/view/' . $id);
    }

    public function delete(string $id): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('proposals.delete');
        $this->validateCsrf();

        $this->model->delete((int)$id);
        auditLog('delete_proposal', 'proposals', (int)$id);
        Session::flash('success', 'Proposal deleted.');
        $this->redirect('proposals');
    }
}
