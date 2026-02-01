        fetch('header.html')
            .then(function (res) {
                if (!res.ok) throw new Error('Failed to load header');
                return res.text();
            })
            .then(function (html) {
                document.getElementById('site-header').innerHTML = html;
            })
            .catch(function (err) {
                console.warn(err);
            });
                    fetch('footer.html')
            .then(function (res) {
                if (!res.ok) throw new Error('Failed to load footer');
                return res.text();
            })
            .then(function (html) {
                document.getElementById('site-footer').innerHTML = html;
            })
            .catch(function (err) {
                console.warn(err);
            });