<?php
class AttendanceController extends Controller
{
    public function index(): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('attendance.view');
        $this->view('attendance/index', ['pageTitle' => 'Attendance Records']);
    }
    public function record(string $activityId = ''): void
    {
        Middleware::requireAuth();
        Middleware::requirePermission('attendance.record');
        $this->view('attendance/record', ['pageTitle' => 'Record Attendance', 'activityId' => $activityId]);
    }
}
