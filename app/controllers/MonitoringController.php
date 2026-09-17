<?php
class MonitoringController extends Controller
{
    public function index(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('monitoring.view');
        $this->view('monitoring/index', ['pageTitle' => 'Monitoring & Progress']);
    }
    public function log(string $proposalId = ''): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('monitoring.log');
        $this->view('monitoring/log', ['pageTitle' => 'Log Progress', 'proposalId' => $proposalId]);
    }
    public function view(string $id): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('monitoring.view');
        $this->view('monitoring/view', ['pageTitle' => 'Monitoring Details', 'id' => $id]);
    }
}
