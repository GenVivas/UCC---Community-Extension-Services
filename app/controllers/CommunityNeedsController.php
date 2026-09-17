<?php

class CommunityNeedsController extends Controller
{
    private CommunityNeedsModel $model;

    public function __construct()
    {
        $this->model = new CommunityNeedsModel();
    }

    // GET /community-needs
    public function index(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('cna.view');

        $page      = max(1, (int)$this->query('page', 1));
        $filters   = [
            'search'     => $this->query('search', ''),
            'status'     => $this->query('status', ''),
            'barangay_id'=> $this->query('barangay_id', ''),
        ];

        $paginator = $this->model->getAllPaginated($page, $filters);
        $barangays = $this->model->getBarangays();

        $this->view('community_needs/index', [
            'pageTitle' => 'Community Needs Assessment',
            'paginator' => $paginator,
            'filters'   => $filters,
            'barangays' => $barangays,
        ]);
    }

    // GET /community-needs/create
    public function create(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('cna.create');

        $barangays = $this->model->getBarangays();
        $this->view('community_needs/create', [
            'pageTitle' => 'New Needs Assessment',
            'barangays' => $barangays,
        ]);
    }

    // POST /community-needs/store
    public function store(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('cna.create');
        $this->validateCsrf();

        $data = [
            'barangay_id'     => $this->input('barangay_id'),
            'assessment_date' => $this->input('assessment_date'),
            'title'           => $this->input('title'),
            'description'     => $this->input('description'),
            'status'          => $this->input('action') === 'submit' ? 'submitted' : 'draft',
            'conducted_by'    => Auth::id(),
        ];

        $v = Validator::make($data)
            ->required('barangay_id',     'Barangay')
            ->required('assessment_date', 'Assessment Date')
            ->required('title',           'Title')
            ->date('assessment_date',     'Assessment Date');

        if ($v->fails()) {
            foreach ($v->allErrors() as $err) Session::flash('error', $err);
            $this->redirect('community-needs/create');
        }

        $this->beginTransaction();
        try {
            $assessmentId = $this->model->create($data);

            // Save need items
            $areas    = $_POST['area']               ?? [];
            $problems = $_POST['problem']            ?? [];
            $sevs     = $_POST['severity']           ?? [];
            $recs     = $_POST['recommended_action'] ?? [];

            foreach ($areas as $i => $area) {
                if (empty(trim($problems[$i] ?? ''))) continue;
                $this->model->addItem([
                    'assessment_id'      => $assessmentId,
                    'area'               => $area,
                    'problem'            => $problems[$i],
                    'severity'           => $sevs[$i] ?? 'medium',
                    'recommended_action' => $recs[$i] ?? null,
                ]);
            }

            $this->commit();
            auditLog('create_assessment', 'cna', (int)$assessmentId, "Created: {$data['title']}");
            Session::flash('success', 'Needs assessment saved successfully.');
            $this->redirect('community-needs/view/' . $assessmentId);
        } catch (Throwable $e) {
            $this->rollback();
            error_log($e->getMessage());
            Session::flash('error', 'Something went wrong. Please try again.');
            $this->redirect('community-needs/create');
        }
    }

    // GET /community-needs/view/{id}
    public function view(string $id): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('cna.view');

        $assessment = $this->model->findById((int)$id);
        if (!$assessment) $this->abort(404, 'Assessment not found.');

        $items = $this->model->getItems((int)$id);

        $this->view('community_needs/view', [
            'pageTitle'  => 'View Assessment',
            'assessment' => $assessment,
            'items'      => $items,
        ]);
    }

    // GET /community-needs/edit/{id}
    public function edit(string $id): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('cna.edit');

        $assessment = $this->model->findById((int)$id);
        if (!$assessment) $this->abort(404, 'Assessment not found.');
        if ($assessment['status'] === 'approved') {
            Session::flash('error', 'Approved assessments cannot be edited.');
            $this->redirect('community-needs/view/' . $id);
        }

        $barangays = $this->model->getBarangays();
        $items     = $this->model->getItems((int)$id);

        $this->view('community_needs/edit', [
            'pageTitle'  => 'Edit Assessment',
            'assessment' => $assessment,
            'barangays'  => $barangays,
            'items'      => $items,
        ]);
    }

    // POST /community-needs/update/{id}
    public function update(string $id): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('cna.edit');
        $this->validateCsrf();

        $data = [
            'barangay_id'     => $this->input('barangay_id'),
            'assessment_date' => $this->input('assessment_date'),
            'title'           => $this->input('title'),
            'description'     => $this->input('description'),
            'status'          => $this->input('action') === 'submit' ? 'submitted' : 'draft',
        ];

        $v = Validator::make($data)
            ->required('barangay_id',     'Barangay')
            ->required('assessment_date', 'Assessment Date')
            ->required('title',           'Title');

        if ($v->fails()) {
            foreach ($v->allErrors() as $err) Session::flash('error', $err);
            $this->redirect('community-needs/edit/' . $id);
        }

        $this->beginTransaction();
        try {
            $this->model->update((int)$id, $data);
            $this->model->deleteAllItems((int)$id);

            $areas    = $_POST['area']               ?? [];
            $problems = $_POST['problem']            ?? [];
            $sevs     = $_POST['severity']           ?? [];
            $recs     = $_POST['recommended_action'] ?? [];

            foreach ($areas as $i => $area) {
                if (empty(trim($problems[$i] ?? ''))) continue;
                $this->model->addItem([
                    'assessment_id'      => $id,
                    'area'               => $area,
                    'problem'            => $problems[$i],
                    'severity'           => $sevs[$i] ?? 'medium',
                    'recommended_action' => $recs[$i] ?? null,
                ]);
            }

            $this->commit();
            auditLog('update_assessment', 'cna', (int)$id);
            Session::flash('success', 'Assessment updated successfully.');
            $this->redirect('community-needs/view/' . $id);
        } catch (Throwable $e) {
            $this->rollback();
            Session::flash('error', 'Update failed. Please try again.');
            $this->redirect('community-needs/edit/' . $id);
        }
    }

    // POST /community-needs/approve/{id}
    public function approve(string $id): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('cna.approve');
        $this->validateCsrf();

        $this->model->approve((int)$id, Auth::id());
        auditLog('approve_assessment', 'cna', (int)$id);
        Session::flash('success', 'Assessment approved.');
        $this->redirect('community-needs/view/' . $id);
    }

    // POST /community-needs/delete/{id}
    public function delete(string $id): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('cna.delete');
        $this->validateCsrf();

        $this->model->delete((int)$id);
        auditLog('delete_assessment', 'cna', (int)$id);
        Session::flash('success', 'Assessment deleted.');
        $this->redirect('community-needs');
    }
}
