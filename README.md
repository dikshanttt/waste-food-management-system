# FoodShare

A small student project for sharing extra food. This version includes only the home, login, and registration pages.

## Files

- index.php is the home page. It checks the member count, then displays the HTML.
- login.php checks an email and password.
- register.php creates a donor or recipient account.
- database/db.php opens the database connection.
- database/schema.sql creates the database and users table.
- assets/css/style.css sets the page colours and layout.
- assets/js/main.js controls the show/hide password button.
- docs/ contains the original project planning documents.

## Run the project

1. Start MySQL.
2. Import database/schema.sql in phpMyAdmin.
3. Check the database name and password in database/db.php.
4. Open a terminal in this folder and run:

   php -S 127.0.0.1:8000

5. Open http://127.0.0.1:8000.

The login and registration forms need MySQL. The home page still opens if MySQL has not been set up yet.

This version stops after login and registration. It does not include donor, recipient, or admin dashboards.
