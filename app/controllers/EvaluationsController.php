<?php
class EvaluationsController extends Controller
{
    public function index(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('evaluations.view');
        $this->view('evaluations/index', ['pageTitle' => 'Evaluations']);
    }
    public function submit(string $activityId = ''): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('evaluations.submit');
        $this->view('evaluations/submit', ['pageTitle' => 'Submit Evaluation', 'activityId' => $activityId]);
    }
    public function results(string $activityId = ''): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('evaluations.results');
        $this->view('evaluations/results', ['pageTitle' => 'Evaluation Results', 'activityId' => $activityId]);
    }
}
