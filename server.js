const express = require('express');
const mongoose = require('mongoose');
const bodyParser = require('body-parser');
const cors = require('cors');
const Batch = require('./models/Batch');
const Distribution = require('./models/Distribution');

const app = express();
const PORT = process.env.PORT || 5000;

// Middleware
app.use(cors());
app.use(bodyParser.json());

// MongoDB Connection
mongoose.connect('mongodb://localhost:27017/distributionDB', { useNewUrlParser: true, useUnifiedTopology: true })
    .then(() => console.log('MongoDB Connected'))
    .catch(err => console.log(err));

// Routes
app.get('/batches', async (req, res) => {
    try {
        const batches = await Batch.find();
        res.json(batches);
    } catch (err) {
        res.status(500).json({ message: 'Server error' });
    }
});

app.post('/batches', async (req, res) => {
    const { batchId, batchName, totalQuantity, remainingQuantity } = req.body;
    try {
        const newBatch = new Batch({
            batchId,
            batchName,
            totalQuantity,
            remainingQuantity
        });
        await newBatch.save();
        res.status(201).json(newBatch);
    } catch (err) {
        res.status(500).json({ message: 'Server error' });
    }
});

app.post('/distributions', async (req, res) => {
    const { batchId, distributedQuantity, distributedTo, dateDistributed } = req.body;
    try {
        const newDistribution = new Distribution({
            batchId,
            distributedQuantity,
            distributedTo,
            dateDistributed
        });

        // Find the batch and update it
        const batch = await Batch.findOne({ batchId });
        if (!batch) return res.status(404).json({ message: 'Batch not found' });

        batch.distributions.push(newDistribution);
        batch.remainingQuantity -= distributedQuantity;
        await batch.save();
        await newDistribution.save();
        res.status(201).json(newDistribution);
    } catch (err) {
        res.status(500).json({ message: 'Server error' });
    }
});

app.get('/history', async (req, res) => {
    try {
        const history = await Distribution.find();
        res.json(history);
    } catch (err) {
        res.status(500).json({ message: 'Server error' });
    }
});

// Start server
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
