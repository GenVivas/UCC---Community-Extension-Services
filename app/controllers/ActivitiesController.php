<?php
class ActivitiesController extends Controller
{
    public function index(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('activities.view');
        $this->view('activities/index', ['pageTitle' => 'Activities']);
    }
}
