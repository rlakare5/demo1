            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(url, name) {
            if (confirm('Are you sure you want to delete "' + name + '"?')) {
                window.location.href = url;
            }
        }
    </script>
</body>
</html>
