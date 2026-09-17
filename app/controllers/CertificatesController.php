<?php
class CertificatesController extends Controller
{
    public function index(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('certificates.view');
        $this->view('certificates/index', ['pageTitle' => 'Certificates']);
    }
    public function generate(string $activityId = ''): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('certificates.generate');
        $this->view('certificates/generate', ['pageTitle' => 'Generate Certificates', 'activityId' => $activityId]);
    }
}
