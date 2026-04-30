<div class="modal-container">
    <div class="main-modal update-modal">
        <div class="modal-body" style="max-width: 600px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Appraisal KPI</h5>
                <button type="button" class="btn-close shadow-none hide-modal-2" aria-label="Close">
                </button>
            </div>
            <hr>
            <div class="modal-content" id="UPDATE_KPI_FORM">

            </div>
        </div>
    </div>

    <script>
        let hideModal2 = document.querySelector('.hide-modal-2');
        let updateModal = document.querySelector('.update-modal');
        let showModal2 = document.querySelectorAll('.show-modal-2');

        hideModal2.addEventListener('click', function() {
            updateModal.style.transform = "translateY(100%)";
            setTimeout(() => {
                updateModal.style.display = "none";
            }, 200);
        });

        // showModal2.forEach(modal => {
        //     modal.addEventListener('click', function() {
        //         updateModal.style.display = "flex";
        //         setTimeout(() => {
        //             updateModal.style.transform = "translateY(0)";
        //         }, 10);
        //     });
        // });

        //fetching form data via ajax 
        try {
            let showModal2 = document.querySelectorAll('.show-modal-2');
            showModal2.forEach(modal => {
                modal.addEventListener('click', function(e) {
                    e.preventDefault();
                    updateModal.style.display = "flex";
                    setTimeout(() => {
                        updateModal.style.transform = "translateY(0)";
                    }, 10);


                    let url = this.getAttribute('href');
                    console.log(url);


                    fetch(url)
                        .then(response => response.text())
                        .then(data => {
                            // Parse response into a temporary DOM
                            let tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data;

                            // console.log(data);

                            document.querySelector('#UPDATE_KPI_FORM').innerHTML = data;


                        })
                        .catch(error => console.error('Error loading modal content:', error));

                });
            });
        } catch (err) {
            console.log(err);
        }

        // hidding modal when clicked outside the modal
        window.addEventListener('click', function(event) {
            if (event.target === updateModal) {
                updateModal.style.transform = "translateY(100%)";
                setTimeout(() => {
                    updateModal.style.display = "none";
                }, 200);
            }
        });
    </script>
</div>