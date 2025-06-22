const express = require('express');
const mongoose = require('mongoose');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 5000;

app.use(express.json());

mongoose.connect(process.env.MONGODB_URI || 'mongodb+srv://ds85:NameoftheWind@gwatch-initial-db.osqutfb.mongodb.net/?retryWrites=true&w=majority&appName=Gwatch-Initial-db')
  .then(() => console.log('Connected to MongoDB'))
  .catch(err => console.error('MongoDB connection error:', err));

app.get('/', (req, res) => {
  res.json({ message: 'Gwatch API is running' });
});

app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
}); 