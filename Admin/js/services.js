 
        function toggleForm(formId) {
            const form = document.getElementById(formId);
            // Toggle visibility: If it's already visible, hide it. If not, show it.
            form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
        }


        //date and time in PH.
        function updateDateTimePH() {
            const options = { timeZone: 'Asia/Manila', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true };
            const dateTimePH = new Date().toLocaleString('en-PH', options);
            document.getElementById('dateTimePH').textContent = dateTimePH;
          }
          setInterval(updateDateTimePH, 1000);
          updateDateTimePH();



          function toggleForm(formId, clickedElement) {
            const form = document.getElementById(formId);
            const isVisible = form.classList.contains("show");
        
            // Hide all other forms
            document.querySelectorAll(".service-form").forEach(f => f.classList.remove("show"));
            document.querySelectorAll(".arrow-icon").forEach(icon => icon.classList.remove("rotate"));
        
            // Show only the clicked one
            if (!isVisible) {
                form.classList.add("show");
                clickedElement.querySelector(".arrow-icon").classList.add("rotate");
            }
        }
        