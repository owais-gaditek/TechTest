// resources/js/fibonacci.js

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.fibonacci-form');
    const resultDiv = document.querySelector('.fibonacci-results');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const input = document.querySelector('input[name="n"]');
        const n = input.value;

        try {
            const response = await fetch('/fibonacci', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ n }),
            });

            const data = await response.json();
            if (data.success) {
                resultDiv.innerHTML = `<p>The Fibonacci sequence up to ${n} is: ${data.sequence.join(', ')}</p>`;
            } else {
                resultDiv.innerHTML = `<p>Error: ${data.message}</p>`;
            }
        } catch (error) {
            resultDiv.innerHTML = `<p>Something went wrong: ${error.message}</p>`;
        }
    });
});
