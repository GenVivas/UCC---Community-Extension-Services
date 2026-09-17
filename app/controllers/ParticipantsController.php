<?php
class ParticipantsController extends Controller
{
    private ParticipantModel $model;
    public function __construct() { $this->model = new ParticipantModel(); }

    public function index(): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('participants.view');
        $page = max(1,(int)$this->query('page',1));
        $filters = ['search'=>$this->query('search',''),'barangay_id'=>$this->query('barangay_id',''),'category'=>$this->query('category','')];
        $this->view('participants/index',['pageTitle'=>'Participants','paginator'=>$this->model->getAllPaginated($page,$filters),'filters'=>$filters,'barangays'=>$this->model->getBarangays()]);
    }

    public function create(): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('participants.manage');
        $this->view('participants/create',['pageTitle'=>'Add Participant','barangays'=>$this->model->getBarangays()]);
    }

    public function store(): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('participants.manage'); $this->validateCsrf();
        $d = [':barangay_id'=>$this->input('barangay_id'),':first_name'=>$this->input('first_name'),':last_name'=>$this->input('last_name'),':birthdate'=>$this->input('birthdate')?:null,':gender'=>$this->input('gender'),':civil_status'=>$this->input('civil_status'),':address'=>$this->input('address'),':contact_no'=>$this->input('contact_no'),':category'=>$this->input('category','other')];
        $v = Validator::make($_POST)->required('barangay_id','Barangay')->required('first_name','First Name')->required('last_name','Last Name');
        if($v->fails()){foreach($v->allErrors() as $e) Session::flash('error',$e); $this->redirect('participants/create');}
        $id = $this->model->create($d);
        auditLog('create_participant','participants',(int)$id);
        Session::flash('success','Participant added.'); $this->redirect('participants/view/'.$id);
    }

    public function view(string $id): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('participants.view');
        $participant = $this->model->findById((int)$id);
        if(!$participant) $this->abort(404);
        $history = $this->model->getActivityHistory((int)$id);
        $this->view('participants/view',['pageTitle'=>'Participant Details','participant'=>$participant,'history'=>$history]);
    }

    public function edit(string $id): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('participants.manage');
        $participant = $this->model->findById((int)$id);
        if(!$participant) $this->abort(404);
        $this->view('participants/edit',['pageTitle'=>'Edit Participant','participant'=>$participant,'barangays'=>$this->model->getBarangays()]);
    }

    public function update(string $id): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('participants.manage'); $this->validateCsrf();
        $d = [':barangay_id'=>$this->input('barangay_id'),':first_name'=>$this->input('first_name'),':last_name'=>$this->input('last_name'),':birthdate'=>$this->input('birthdate')?:null,':gender'=>$this->input('gender'),':civil_status'=>$this->input('civil_status'),':address'=>$this->input('address'),':contact_no'=>$this->input('contact_no'),':category'=>$this->input('category','other')];
        $this->model->update((int)$id,$d);
        Session::flash('success','Participant updated.'); $this->redirect('participants/view/'.$id);
    }
}
