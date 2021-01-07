self.addEventListener('push', function(event) {
    console.log(event)
    event.waitUntil(
        self.registration.showNotification('NMS Prime Ticket System', {
            body: 'NMS Prime Ticket System',
        })
    );
});
