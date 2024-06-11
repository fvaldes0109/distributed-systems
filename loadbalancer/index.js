require('dotenv').config();
const express = require('express');
const axios = require('axios');

const app = express();

const urls = process.env.BACKEND_SERVICES_URLS.split(',');
const hosts = process.env.BACKEND_SERVICES_HOSTS.split(',');

let availableServices = [];

setInterval(async () => {
    availableServices = await refreshAvailableServices();
}, 2000);

app.get('*', async (req, res) => {
    const randomUrl = availableServices[Math.floor(Math.random() * availableServices.length)];
    res.redirect(randomUrl + req.url);
});

app.post('*', async (req, res) => {
    const randomUrl = availableServices[Math.floor(Math.random() * availableServices.length)];
    res.redirect(307, randomUrl + req.url);
});

app.listen(process.env.LOAD_BALANCER_PORT, () => {
    console.log('Load balancer is running on port ' + process.env.LOAD_BALANCER_PORT);
});

const refreshAvailableServices = async () => {
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
