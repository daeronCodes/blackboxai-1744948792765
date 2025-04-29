
Built by https://www.blackbox.ai

---

```markdown
# Barangay Attendance Monitoring System

## Project Overview
The Barangay Attendance Monitoring System is a web application designed to facilitate the management of activities and attendance records for officials within a barangay. This application provides an intuitive interface for users to add, edit, and delete activities, as well as mark attendance for particular events. The system also allows for report generation on attendance records over a specified date range.

## Installation
To set up the project locally:

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/barangay-attendance-system.git
   cd barangay-attendance-system
   ```

2. **Set up the environment**:
   - Ensure you have PHP and a web server (like Apache or Nginx) configured.
   - Use a database management tool such as phpMyAdmin to create a new database.

3. **Configure the database**:
   - Update the `config/database.php` file with your database credentials.
   - Run the SQL queries to create the necessary tables (you can find them in the project files).

4. **Run the application**:
   - Place the project files in your web server’s root directory.
   - Access the application from your web browser (e.g. `http://localhost/barangay-attendance-system/index.php`).

## Usage
After installation, you can:
- **Register** a new account or log in as an existing user.
- Navigate to the **Dashboard** to view upcoming activities and recent attendance.
- Use the **Activities Management** section to add or manage activities.
- Mark attendance for activities in the **Attendance** section.
- Generate attendance reports based on officials and date ranges.

## Features
- User authentication and registration.
- Add, edit, and delete activities.
- Mark attendance for activities based on status.
- Generate and view reports for attendance records filtered by officials and date range.
- Responsive UI design for ease of use.

## Dependencies
Make sure you have the following dependencies installed:
- PHP 7.2 or higher
- MySQL or MariaDB
- A web server like Apache or Nginx
- Required PHP extensions (PDO, session)

## Project Structure
```plaintext
barangay-attendance-system/
├── activities.php            # Manages activities (CRUD operations)
├── attendance.php            # Handles attendance marking
├── dashboard.php             # User dashboard showing activities and attendance
├── index.php                 # Main landing page (login)
├── officials.php             # Management of officials (CRUD)
├── reports.php               # Generates attendance reports
├── process_logout.php        # Handles user logout
├── config/
│   └── database.php          # Database connection logic
└── includes/
    ├── header.php            # Header template
    └── footer.php            # Footer template
```

## Conclusion
The Barangay Attendance Monitoring System provides an effective way to manage activities and attendance in a barangay setting. With easy installation and a user-friendly interface, it is an ideal solution for officials requiring attendance oversight.
```