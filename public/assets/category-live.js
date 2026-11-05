(() => {
    const input = document.querySelector('#cat-q');
    const statusSelect = document.querySelector('#cat-status');
    const msg = document.querySelector('#cat-msg');
    const retry = document.querySelector('#cat-retry');
    const results = document.querySelector('#cat-results');

    if (!input || !statusSelect || !msg || !retry || !results) {
        return;
    }

    let timer = null;
    let controller = null; // huỷ request cũ khi người dùng gõ tiếp

    function setLoading(isLoading) {
        results.setAttribute('aria-busy', String(isLoading));
        input.disabled = false; // vẫn cho gõ tiếp trong lúc tải
        if (isLoading) {
            msg.className = 'live-msg loading';
            msg.textContent = 'Đang tải...';
            retry.hidden = true;
        }
    }

    function showError(text, canRetry = true, loginLink = false) {
        results.replaceChildren();
        msg.className = 'live-msg error';
        msg.textContent = text;

        if (loginLink) {
            const a = document.createElement('a');
            a.href = 'index.php?route=login';
            a.textContent = ' Đăng nhập lại';
            msg.appendChild(a);
        }

        retry.hidden = !canRetry;
    }

    function render(rows) {
        results.replaceChildren();

        if (rows.length === 0) {
            msg.className = 'live-msg muted';
            msg.textContent = 'Không có danh mục phù hợp.';
            return;
        }

        const table = document.createElement('table');
        const thead = document.createElement('thead');
        const headRow = document.createElement('tr');
        for (const title of ['ID', 'Tên danh mục', 'Trạng thái', 'Số sản phẩm']) {
            const th = document.createElement('th');
            th.textContent = title;
            headRow.appendChild(th);
        }
        thead.appendChild(headRow);

        const tbody = document.createElement('tbody');
        for (const row of rows) {
            const tr = document.createElement('tr');
            for (const value of [row.id, row.name, row.status, row.product_count]) {
                const td = document.createElement('td');
                td.textContent = String(value); // textContent: chống XSS
                tr.appendChild(td);
            }
            tbody.appendChild(tr);
        }

        table.append(thead, tbody);
        results.appendChild(table);

        msg.className = 'live-msg muted';
        msg.textContent = `Tìm thấy ${rows.length} danh mục.`;
    }

    async function load() {
        if (controller) {
            controller.abort();
        }
        controller = new AbortController();
        const mine = controller;

        const params = new URLSearchParams({
            route: 'api/categories',
            q: input.value.trim(),
            status: statusSelect.value,
        });

        setLoading(true);

        try {
            const response = await fetch('index.php?' + params.toString(), {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
                signal: controller.signal,
            });

            // Server luôn trả JSON kể cả khi lỗi -> đọc message nếu có.
            let json = null;
            try {
                json = await response.json();
            } catch (parseError) {
                json = null;
            }

            if (!response.ok) {
                const serverMsg = json && json.message ? json.message : 'Lỗi máy chủ (HTTP ' + response.status + ').';
                showError(serverMsg, response.status >= 500, response.status === 401);
                return;
            }

            if (!json || json.ok !== true || !Array.isArray(json.data)) {
                showError('Dữ liệu trả về không đúng định dạng.');
                return;
            }

            render(json.data);
        } catch (error) {
            if (error.name === 'AbortError') {
                return; // request cũ bị huỷ có chủ đích, không báo lỗi
            }
            console.error(error);
            showError('Không kết nối được máy chủ. Kiểm tra mạng rồi thử lại.');
        } finally {
            if (controller === mine) {
                setLoading(false); // chỉ tắt aria-busy nếu chưa có request mới hơn
            }
        }
    }

    function schedule() {
        clearTimeout(timer);
        timer = setTimeout(load, 300); // debounce 300 ms
    }

    input.addEventListener('input', schedule);
    statusSelect.addEventListener('change', load);
    retry.addEventListener('click', load);

    load(); // tải lần đầu khi mở trang
})();
