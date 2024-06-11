require('dotenv').config();
const express = require('express');

const app = express();

// Read and parse the URLs from the environment variable
const urls = process.env.BACKEND_SERVICES_URLS.split(',');

app.get('*', (req, res) => {
    const randomUrl = urls[Math.floor(Math.random() * urls.length)];
    res.redirect(randomUrl + req.url);
});

app.post('*', (req, res) => {
    const randomUrl = urls[Math.floor(Math.random() * urls.length)];
    res.redirect(307, randomUrl + req.url);
});

app.listen(process.env.LOAD_BALANCER_PORT, () => {
    console.log('Load balancer is running on port ' + process.env.LOAD_BALANCER_PORT);
});
