<?php
class FinancialController extends Controller
{
    public function index(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('financial.view');
        $this->view('financial/index', ['pageTitle' => 'Financial Records']);
    }
    public function create(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('financial.manage');
        $this->view('financial/create', ['pageTitle' => 'Add Financial Record']);
    }
    public function report(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('financial.view');
        $this->view('financial/report', ['pageTitle' => 'Financial Report']);
    }
}
