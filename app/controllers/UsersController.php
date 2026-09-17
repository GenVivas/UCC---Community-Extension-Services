<?php
class UsersController extends Controller
{
    private UserModel $model;
    public function __construct() { $this->model = new UserModel(); }

    public function index(): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('users.view');
        $page    = max(1,(int)$this->query('page',1));
        $filters = ['search'=>$this->query('search',''),'role'=>$this->query('role','')];
        $this->view('users/index',['pageTitle'=>'User Management','paginator'=>$this->model->getAllPaginated($page,$filters),'filters'=>$filters]);
    }

    public function create(): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('users.create');
        $this->view('users/create',['pageTitle'=>'Create User','programs'=>$this->model->getPrograms()]);
    }

    public function store(): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('users.create'); $this->validateCsrf();
        $data = ['first_name'=>$this->input('first_name'),'last_name'=>$this->input('last_name'),'email'=>$this->input('email'),'password'=>$_POST['password']??'','password_confirm'=>$_POST['password_confirm']??'','role'=>$this->input('role','student'),'program_id'=>$this->input('program_id')?:null,'employee_id'=>$this->input('employee_id'),'contact_no'=>$this->input('contact_no')];
        $v = Validator::make($data)->required('first_name','First Name')->required('last_name','Last Name')->required('email','Email')->email('email','Email')->required('role','Role')->minLength('password',8,'Password')->confirmed('password','password_confirm','Password');
        if($v->fails()){foreach($v->allErrors() as $e) Session::flash('error',$e); $this->redirect('users/create');}
        if($this->model->emailExists($data['email'])){Session::flash('error','Email already exists.'); $this->redirect('users/create');}
        $id = $this->model->create($data);
        auditLog('create_user','users',(int)$id,"Created: {$data['email']}");
        Session::flash('success','User created successfully.'); $this->redirect('users');
    }

    public function edit(string $id): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('users.edit');
        $user = $this->model->findById((int)$id);
        if(!$user) $this->abort(404);
        $this->view('users/edit',['pageTitle'=>'Edit User','editUser'=>$user,'programs'=>$this->model->getPrograms()]);
    }

    public function update(string $id): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('users.edit'); $this->validateCsrf();
        $data = ['first_name'=>$this->input('first_name'),'last_name'=>$this->input('last_name'),'email'=>$this->input('email'),'role'=>$this->input('role'),'program_id'=>$this->input('program_id')?:null,'employee_id'=>$this->input('employee_id'),'contact_no'=>$this->input('contact_no'),'is_active'=>(int)$this->input('is_active',1)];
        if($this->model->emailExists($data['email'],(int)$id)){Session::flash('error','Email already taken.'); $this->redirect('users/edit/'.$id);}
        $this->model->update((int)$id,$data);
        if(!empty($_POST['new_password'])){$this->model->updatePassword((int)$id,$_POST['new_password']);}
        auditLog('update_user','users',(int)$id);
        Session::flash('success','User updated.'); $this->redirect('users');
    }

    public function deactivate(string $id): void
    {
        Middleware::requireAuth(); Middleware::requirePermission('users.delete'); $this->validateCsrf();
        if($id == Auth::id()){Session::flash('error','You cannot deactivate your own account.'); $this->redirect('users'); return;}
        $this->model->deactivate((int)$id);
        auditLog('deactivate_user','users',(int)$id);
        Session::flash('success','User deactivated.'); $this->redirect('users');
    }
}
