
        const links = document.querySelectorAll('#rules-toc .toc-link');
        const sections = [...document.querySelectorAll('.rule-card[id]')];
        const setActive = (id) => links.forEach(l => l.classList.toggle('active', l.dataset.target === id));
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) setActive(e.target.id); });
        }, { rootMargin: '-40% 0px -55% 0px', threshold: 0 });
        sections.forEach(s => io.observe(s));
        links.forEach(l => l.addEventListener('click', (ev) => {
            const id = l.getAttribute('href').slice(1);
            const el = document.getElementById(id);
            if (el) { ev.preventDefault(); el.scrollIntoView({ behavior: 'smooth', block: 'start' }); history.replaceState(null, '', '#' + id); }
        }));