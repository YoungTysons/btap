<?php
/**
 * Plugin Name: LearnPress Stats Dashboard
 * Description: Hiển thị thống kê LearnPress trong Admin Dashboard và Shortcode.
 * Version: 1.0
 * Author: [Tên của bạn]
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Hàm xử lý lấy dữ liệu từ cơ sở dữ liệu
function lps_get_stats_data() {
    global $wpdb;
    
    // 1. Lấy tổng số khóa học
    $total_courses = wp_count_posts('lp_course')->publish;
    
    // 2. Lấy tổng số học viên (Duy nhất)
    $table_items = $wpdb->prefix . 'learnpress_user_items';
    $total_students = $wpdb->get_var("SELECT COUNT(DISTINCT user_id) FROM $table_items WHERE item_type = 'lp_course'");
    
    // 3. Lấy số lượng khóa học đã hoàn thành
    $completed_courses = $wpdb->get_var("SELECT COUNT(*) FROM $table_items WHERE item_type = 'lp_course' AND status = 'completed'");
    
    return [
        'courses'   => $total_courses ? $total_courses : 0,
        'students'  => $total_students ? $total_students : 0,
        'completed' => $completed_courses ? $completed_courses : 0
    ];
}

// YÊU CẦU: Tạo Dashboard Widget trong Admin
add_action('wp_dashboard_setup', 'lps_add_dashboard_widget');
function lps_add_dashboard_widget() {
    wp_add_dashboard_widget('lps_stats_widget', 'Hệ thống Thống kê LearnPress', 'lps_display_widget');
}

function lps_display_widget() {
    $data = lps_get_stats_data();
    echo "<ul>
            <li><strong>Tổng khóa học:</strong> {$data['courses']}</li>
            <li><strong>Tổng học viên:</strong> {$data['students']}</li>
            <li><strong>Đã hoàn thành:</strong> {$data['completed']}</li>
          </ul>";
}

// YÊU CẦU: Tạo Shortcode [lp_total_stats]
add_shortcode('lp_total_stats', 'lps_shortcode_callback');
function lps_shortcode_callback() {
    $data = lps_get_stats_data();
    return "
    <div style='border: 1px solid #00a0d2; padding: 15px; border-radius: 5px; background: #f0f8ff;'>
        <h3 style='margin-top:0;'>Thống kê học tập</h3>
        <p>Số khóa học hiện có: <b>{$data['courses']}</b></p>
        <p>Học viên tham gia: <b>{$data['students']}</b></p>
        <p>Khóa học đã hoàn thành: <b>{$data['completed']}</b></p>
    </div>";
}