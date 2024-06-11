const express = require('express');

const app = express();

app.get('/', (req, res) => {
    res.send('Hello from load balancer!');
});

app.listen(8080, () => {
    console.log('Load balancer is running on port 8080');
});
