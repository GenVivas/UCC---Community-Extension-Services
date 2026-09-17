<?php

/**
 * Dashboard Controller
 */
class DashboardController extends Controller
{
    private DashboardModel $model;

    public function __construct()
    {
        $this->model = new DashboardModel();
    }

    public function index(): void
    {
        Middleware::requireAuth();

        $role = Auth::role();
        $user = Auth::user();
        $data = ['pageTitle' => 'Dashboard', 'user' => $user];

        switch ($role) {
            case 'admin':
            case 'ces_head':
                $data['stats']              = $this->model->getAdminStats();
                $data['recentProposals']    = $this->model->getRecentProposals();
                $data['upcomingActivities'] = $this->model->getUpcomingActivities();
                $data['projectsByArea']     = $this->model->getProjectsByArea();
                $data['monthlyActivities']  = $this->model->getMonthlyActivities();
                $view = 'dashboard/admin';
                break;

            case 'program_head':
            case 'faculty':
                $programId            = (int)($user['program_id'] ?? 0);
                $data['stats']        = $this->model->getProgramStats($programId);
                $data['recentProposals']    = $this->model->getRecentProposals(5);
                $data['upcomingActivities'] = $this->model->getUpcomingActivities(5);
                $view = 'dashboard/faculty';
                break;

            case 'student':
            default:
                $data['stats']              = $this->model->getStudentStats((int)$user['id']);
                $data['upcomingActivities'] = $this->model->getUpcomingActivities(5);
                $view = 'dashboard/student';
                break;
        }

        $this->view($view, $data);
    }
}
