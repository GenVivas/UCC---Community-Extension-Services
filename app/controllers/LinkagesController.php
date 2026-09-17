<?php
class LinkagesController extends Controller
{
    public function index(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('linkages.view');
        $this->view('linkages/index', ['pageTitle' => 'Linkages & Partners']);
    }
}
