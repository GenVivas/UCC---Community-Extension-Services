<?php
class AuditController extends Controller
{
    public function index(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('audit.view');
        $this->view('audit/index', ['pageTitle' => 'Audit Logs']);
    }
}
