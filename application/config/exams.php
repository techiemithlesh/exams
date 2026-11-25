<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| Exams build (exams.futurecampus.in)
|
| Permission prefixes of modules that are not part of the exams portal.
| Their rows were deleted from the `permission` table, so other roles can't
| get them. get_permission() also denies them to the super admin, who
| normally skips the DB check, so these pages and dashboard widgets
| stay hidden and blocked for every login.
| Remove a prefix here (and re-add its permission row) to bring a module back.
*/
$config['exams_removed_permissions'] = [
    // Dashboard widgets
    'monthly_income_vs_expense_chart', 'annual_student_fees_summary_chart', 'parent_count_widget',
    'weekend_attendance_inspection_chart', 'voucher_count_widget', 'transport_count_widget', 'hostel_count_widget',
    // Student / Parents
    'student_promotion', 'parent', 'parent_disable_authentication',
    // Human resource
    'salary_template', 'salary_assign', 'salary_payment', 'salary_summary_report', 'advance_salary',
    'advance_salary_manage', 'advance_salary_request', 'leave_category', 'leave_request', 'leave_manage',
    'award', 'leave_reports',
    // Academic, live class, gallery, homework, books
    'class_timetable', 'live_class', 'digital_gallery', 'homework', 'homework_evaluate', 'evaluation_report',
    'attachments', 'attachment_type', 'upload_books_link', 'assign_branch', 'assign_class',
    'book_uploads', 'digital_book', 'student_book_upload_edit',
    // Hostel, transport
    'hostel', 'hostel_category', 'hostel_room', 'hostel_allocation',
    'transport_route', 'transport_vehicle', 'transport_stoppage', 'transport_assign', 'transport_allocation',
    // Attendance (exam attendance stays)
    'student_attendance', 'employee_attendance', 'student_attendance_report', 'employee_attendance_report',
    // Library, events, SMS/email
    'book', 'book_category', 'book_manage', 'book_request', 'event', 'event_type',
    'sendsmsmail', 'sendsmsmail_template', 'sendsmsmail_reports',
    // Office and student accounting
    'account', 'deposit', 'expense', 'all_transactions', 'voucher_head', 'accounting_reports', 'voucher',
    'fees_type', 'fees_group', 'fees_fine_setup', 'fees_allocation', 'collect_fees', 'fees_reminder',
    'due_invoice', 'invoice', 'fees_reports',
];
