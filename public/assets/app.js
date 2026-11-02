(() => {
    const input = document.querySelector('#live-search');
    const results = document.querySelector('#live-results');
    const status = document.querySelector('#live-status');

    if (!input || !results || !status) {
        return;
    }

    let timer = null;

    input.addEventListener('input', () => {
        clearTimeout(timer);

        timer = setTimeout(async () => {
            const keyword = input.value.trim();

            status.textContent = 'Đang tải...';

            try {
                const response = await fetch(
                    'index.php?route=api/products&q=' + encodeURIComponent(keyword),
                    {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                const json = await response.json();

                results.replaceChildren();

                const table = document.createElement('table');
                const thead = document.createElement('thead');
                thead.innerHTML = `
                    <tr>
                        <th>Mã</th>
                        <th>Tên</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                    </tr>
                `;

                const tbody = document.createElement('tbody');

                for (const product of json.data) {
                    const tr = document.createElement('tr');

                    for (const value of [
                        product.code,
                        product.name,
                        product.category_name,
                        Number(product.price).toLocaleString('vi-VN') + ' đ',
                        String(product.stock)
                    ]) {
                        const td = document.createElement('td');
                        // Dữ liệu từ JSON được đưa vào DOM bằng textContent.
                        td.textContent = value;
                        tr.appendChild(td);
                    }

                    tbody.appendChild(tr);
                }

                table.append(thead, tbody);
                results.appendChild(table);

                status.textContent = `Tìm thấy ${json.data.length} sản phẩm.`;
            } catch (error) {
                results.replaceChildren();
                status.textContent = 'Không tải được dữ liệu.';
                console.error(error);
            }
        }, 300);
    });
})();
