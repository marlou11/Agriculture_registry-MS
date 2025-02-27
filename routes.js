// routes/users.js
const express = require('express');
const db = require('../db');

const router = express.Router();

// Get all users
router.get('/', (req, res) => {
    db.query('SELECT * FROM users', (err, results) => {
        if (err) {
            console.error('Error fetching users:', err);
            res.status(500).send('Internal Server Error');
            return;
        }
        res.json(results);
    });
});

// Add a new user
router.post('/', (req, res) => {
    const { first_name, middle_initial, last_name, rsba_number, contact_number, barangay, home_address, farm_size, farm_location, password, status } = req.body;

    const sql = `
        INSERT INTO users (first_name, middle_initial, last_name, rsba_number, contact_number, barangay, home_address, farm_size, farm_location, password, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;

    db.query(sql, [first_name, middle_initial, last_name, rsba_number, contact_number, barangay, home_address, farm_size, farm_location, password, status], (err, result) => {
        if (err) {
            console.error('Error adding user:', err);
            res.status(500).send('Internal Server Error');
            return;
        }
        res.json({ message: 'User added successfully', userId: result.insertId });
    });
});

module.exports = router;
