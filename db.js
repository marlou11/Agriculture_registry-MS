// db.js
const mysql = require('mysql2');

// Create the MySQL connection
const connection = mysql.createConnection({
    host: 'localhost',  // XAMPP runs MySQL on localhost
    user: 'root',       // Default MySQL username in XAMPP
    password: '',       // Default password is empty in XAMPP
    database: 'arms_db', // Name of the database you created
    port: 3306          // Default MySQL port
});

// Connect to MySQL
connection.connect((err) => {
    if (err) {
        console.error('Error connecting to the database:', err);
        return;
    }
    console.log('Successfully connected to the MySQL database!');
});

// Export the connection object so you can use it elsewhere
module.exports = connection;
