    <script>
        // 전역 스코프에 함수들 정의
        window.copyJsonToClipboard = function() {
            const jsonElement = document.getElementById('json-data');
            if (jsonElement) {
                const text = jsonElement.textContent;

                // Clipboard API를 사용 (최신 브라우저)
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        window.showCopySuccess();
                    }).catch(err => {
                        console.error('클립보드 복사 실패:', err);
                        window.fallbackCopyTextToClipboard(text);
                    });
                } else {
                    // fallback (구형 브라우저)
                    window.fallbackCopyTextToClipboard(text);
                }
            }
        };

        window.fallbackCopyTextToClipboard = function(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";

            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    window.showCopySuccess();
                } else {
                    window.showCopyError();
                }
            } catch (err) {
                console.error('Fallback: 클립보드 복사 실패', err);
                window.showCopyError();
            }

            document.body.removeChild(textArea);
        };

        window.showCopySuccess = function() {
            const button = document.querySelector('button[onclick="copyJsonToClipboard()"]');
            if (button) {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-1"></i>복사됨';
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-success');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-primary');
                }, 2000);
            }
        };

        window.showCopyError = function() {
            const button = document.querySelector('button[onclick="copyJsonToClipboard()"]');
            if (button) {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-times me-1"></i>실패';
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-danger');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-danger');
                    button.classList.add('btn-outline-primary');
                }, 2000);
            }
        };

        // DOMContentLoaded 이벤트 리스너
        document.addEventListener('DOMContentLoaded', function() {
            let pollingInterval;

            Livewire.on('start-polling', () => {
                pollingInterval = setInterval(() => {
                    Livewire.dispatch('check-status');
                }, 1000);
            });

            Livewire.on('stop-polling', () => {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                }
            });

            window.addEventListener('beforeunload', () => {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                }
            });
        });
    </script>
