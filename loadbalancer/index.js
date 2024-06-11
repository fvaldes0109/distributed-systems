require('dotenv').config();
const express = require('express');
const axios = require('axios');

const app = express();

const urls = process.env.BACKEND_SERVICES_URLS.split(',');
const hosts = process.env.BACKEND_SERVICES_NAMES.split(',');

app.get('/hello', (req, res) => {
    res.send('Hello from the load balancer!');
});

app.get('/available-services', async (req, res) => {
    res.json(await getAvailableServices());
});

app.get('*', async (req, res) => {

    const availableServices = await getAvailableServices();
    const randomUrl = availableServices[Math.floor(Math.random() * availableServices.length)];
    res.redirect(randomUrl + req.url);
});

app.post('*', async (req, res) => {
    const availableServices = await getAvailableServices();
    const randomUrl = availableServices[Math.floor(Math.random() * availableServices.length)];
    res.redirect(307, randomUrl + req.url);
});

app.listen(process.env.LOAD_BALANCER_PORT, () => {
    console.log('Load balancer is running on port ' + process.env.LOAD_BALANCER_PORT);
});

const getAvailableServices = async () => {
    const servicesResponses = await Promise.all(hosts.map(async (host, index) => {
        try {
            const response = await axios.get(`http://${host}/api/hello`, { timeout: 5000 });
            if (response.status === 200) {
                return urls[index];
            }
        } catch (error) {
            console.error(`Service at ${urls[index]} is not available.`, error.message);
            return null;
        }
    }));

    return servicesResponses.filter(url => url !== null);
}
