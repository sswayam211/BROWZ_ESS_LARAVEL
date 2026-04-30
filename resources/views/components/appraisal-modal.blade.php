<!-- appraisal modal start -->
<div class="modal-container">
    <div class="main-modal appraisal-modal">
        <div class="modal-body" style="max-width: 1200px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Performance Appraisal</h5>
                <button type="button" class="btn-close shadow-none hide-modal-2" aria-label="Close">
                    <!-- <i class="fa-solid fa-xmark"></i> -->
                </button>
            </div>
            <hr>
            <div class="modal-content" id="APPRAISAL_FORM">

            </div>
        </div>
    </div>

    <script>
        // Function to initialize all KPI sliders
        function initSliders() {
            document.querySelectorAll('.kpi-slider').forEach(function(slider) {
                var code = slider.id.replace('EC', '');
                var wrap = document.getElementById('wrap_' + code);
                var thumb = document.getElementById('thumb_' + code);
                var cur = document.getElementById('cur_' + code);
                var min = parseInt(slider.min);
                var max = parseInt(slider.max);

                function updateThumb() {
                    var val = parseInt(slider.value);
                    var pct = (val - min) / (max - min);
                    var trackW = wrap.offsetWidth;
                    var pos = pct * (trackW - 18) + 9;
                    thumb.style.left = pos + 'px';
                    thumb.textContent = val;
                    cur.textContent = val + ' / ' + max;
                }

                slider.addEventListener('input', updateThumb);
                // Init on page load
                setTimeout(updateThumb, 50);
            });
        }

        let hideModal2 = document.querySelector('.hide-modal-2');
        let appraisalModal = document.querySelector('.appraisal-modal');
        let showModal2 = document.querySelectorAll('.show-modal-2');

        hideModal2.addEventListener('click', function() {
            appraisalModal.style.transform = "translateY(100%)";
            setTimeout(() => {
                appraisalModal.style.display = "none";
            }, 200);
        });

        showModal2.forEach(modal => {
            modal.addEventListener('click', function() {
                appraisalModal.style.display = "flex";
                setTimeout(() => {
                    appraisalModal.style.transform = "translateY(0)";
                }, 10);
            });
        });

        //fetching form data via ajax 
        try {
            let showModal2 = document.querySelectorAll('.show-modal-2');
            showModal2.forEach(modal => {
                modal.addEventListener('click', function(e) {
                    e.preventDefault();

                    let url = this.getAttribute('href');
                    console.log(url);
                    document.querySelector('#APPRAISAL_FORM').innerHTML = 'Loading form...';


                    fetch(url)
                        .then(response => response.text())
                        .then(data => {
                            // Parse response into a temporary DOM
                            let tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data;

                            // console.log(data);

                            document.querySelector('#APPRAISAL_FORM').innerHTML = data;

                            // Initialize sliders for newly loaded form
                            initSliders();

                        })
                        .catch(error => console.error('Error loading modal content:', error));

                });
            });
        } catch (err) {
            console.log(err);
        }

        // hidding modal when clicked outside the modal
        window.addEventListener('click', function(event) {
            if (event.target === appraisalModal) {
                appraisalModal.style.transform = "translateY(100%)";
                setTimeout(() => {
                    appraisalModal.style.display = "none";
                }, 200);
            }
        });




        // SENDING FORM DATA TO BACKEND THROUGH AJAX
        document.addEventListener('submit', function(e) {
            if (e.target && e.target.id === 'appraisal-form-submit') {
                e.preventDefault();
                const form = e.target;
                const url = form.action;
                const formData = new FormData(form);
                // const empCode = formData.get('EMP_CODE');

                console.log('SEND DATA ON : ', url);
                // console.log('form:', form.querySelector('#SAVE_APPRAISAL_DATA').getAttribute('name'));


                if (form.querySelector('#SAVE_APPRAISAL_DATA').getAttribute('name') === 'SAVE_APPRAISAL') {
                    formData.append("SAVE_APPRAISAL", "1");
                } else if (form.querySelector('#SAVE_APPRAISAL_DATA').getAttribute('name') === 'SUBMIT_APPRAISAL') {
                    formData.append("SUBMIT_APPRAISAL", "1");
                } else if (form.querySelector('#SAVE_APPRAISAL_DATA').getAttribute('name') === 'APPROVE_APPRAISAL') {
                    formData.append("APPROVE_APPRAISAL", "1");
                }


                // making fetch link to fetch form after form is submited
                const empCode = formData.get('EMP_CODE');
                const ID = formData.get('APPRAISAL_SYS_ID');
                // console.log('EMP CODE AND ID : ', empCode, ID);

                let isApproveApprasial = formData.get('FORM_TYPE');
                // console.log(isApproveApprasial);

                let getFormUrl = '';

                if (isApproveApprasial === 'APPROVE') {
                    getFormUrl = 'approve_appraisal_form?status=appr&appr_code=' + encodeURIComponent(ID);
                } else {
                    getFormUrl = 'appraisal_form?APRSL_EMP_CODE=' + encodeURIComponent(empCode);
                }

                // console.log('FETCHING NEXT KPI URL : ', getFormUrl);




                // disable submit button while request is in flight
                const submitBtn = form.querySelector('button[type=submit]');
                let originalBtnText = '';
                let refreshingNext = false;

                // show overlay spinner on modal
                function showModalLoading() {
                    const overlay = document.createElement('div');
                    overlay.className = 'modal-loading-overlay';
                    overlay.id = 'appraisal-loading';
                    overlay.style.position = 'absolute';
                    overlay.style.top = '0';
                    overlay.style.left = '0';
                    overlay.style.right = '0';
                    overlay.style.bottom = '0';
                    overlay.style.background = 'rgba(255,255,255,0.7)';
                    overlay.style.display = 'flex';
                    overlay.style.alignItems = 'center';
                    overlay.style.justifyContent = 'center';
                    overlay.innerHTML = '<i class="fa fa-spinner fa-spin fa-3x"></i>';
                    const container = document.querySelector('#APPRAISAL_FORM');
                    if (container) {
                        // make sure it can hold absolutely positioned overlay
                        container.style.position = 'relative';
                        container.appendChild(overlay);
                    }
                }

                function hideModalLoading() {
                    const o = document.getElementById('appraisal-loading');
                    if (o) o.remove();
                }

                showModalLoading();

                if (submitBtn) {
                    // store original text and show spinner
                    originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
                }

                // send data to backend through ajax
                fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        // console.log(data);


                        if (data.trim() === 'APPRAISAL_SAVED' || data.trim() === 'APPRAISAL_SUBMITED' || data.trim() === 'APPRAISAL_APPROVED') {

                            if (data.trim() === 'APPRAISAL_SUBMITED') {
                                alert('Appraisal Submitted Successfully');
                                window.location.reload();
                            } else if (data.trim() === 'APPRAISAL_APPROVED') {
                                alert('Appraisal approved Successfully');
                                window.location.reload();
                            }


                            if (empCode) {
                                // reload next evaluation piece and keep spinner until done
                                refreshingNext = true;
                                fetch(getFormUrl)
                                    .then(r => r.text())
                                    .then(html => {
                                        document.querySelector('#APPRAISAL_FORM').innerHTML = html;
                                        // Initialize sliders for newly fetched form
                                        initSliders();
                                    })
                                    .catch(err => console.error('Error loading next appraisal form:', err))
                                    .finally(() => {
                                        if (submitBtn) {
                                            submitBtn.disabled = false;
                                            submitBtn.innerHTML = originalBtnText;
                                        }
                                        hideModalLoading();
                                    });
                            }
                        } else {
                            console.warn('Unexpected response from appraisal save:', data);
                            // show message to user if something went wrong
                            alert('Unable to save appraisal: ' + data);
                        }
                    })
                    .catch(err => {
                        console.error('Error submitting appraisal form:', err);
                        alert('Unable to submit appraisal. Please try again.');
                    })
                    .finally(() => {
                        // if we are not fetching next, reset the button here
                        if (!refreshingNext && submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                        hideModalLoading();
                    });
            }
        });
    </script>
</div>
<!-- appraisal modal end -->
