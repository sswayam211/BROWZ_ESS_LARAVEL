<div>
    <style>
        #MESSAGE_MODAL {
            display: none;
            position: absolute;
            top: 0px;
            left: 0;
            right: 0;
            bottom: 0;
            /* background: rgba(255, 255, 255, 0.5);
                backdrop-filter: blur(5px) contrast(.5); */
            height: 100vh;
            z-index: 9999;
            transition: all 0.3s ease-in-out;
            transform: translateY(100%);
        }

        #MESSAGE_MODAL:after {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            content: '';
            /* background: #0000002b; */
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px) contrast(.5);
            z-index: -1;
        }

        .icon-box {
            height: 120px;
            color: white;
            text-align: center;
            align-content: center;
            font-size: 40px;
            margin-top: 80px;
        }

        .top-right {
            position: absolute;
            top: 0;
            right: 0;
            background: none;
            border: none;
            color: white;
        }

        .body-modal {
            padding: 10px;
            padding-bottom: 25px;
            background: white;
            color: black;
        }

        #hide-message-modal {
            margin-top: 10px;
        }

        .back-grey {
            background-color: #5e5c5b;
        }
    </style>

    @if (session('SESSION_MESSAGE'))

    <input type="hidden" value="{{ session('SESSION_MESSAGE') }}" id="MODAL_MESSAGE" />

    @php $msg = session('SESSION_MESSAGE'); @endphp

    <div id="MESSAGE_MODAL">
        <div>
            <div class="col-lg-4 col-md-6 col-sm-8 col-11 m-auto">

                {{-- Icon box --}}
                <div class="justify-content-center position-relative back-grey"
                    style="background: '#5e5c5b'">

                    <div class="icon-box">
                        @if (in_array($msg, ['LVAH_ALREADY_EXISTS','FIRST_RESUMPTION_APPLY','LVAH_ALREADY_APPLIED_EXISTS','PLEASE_TRY_AGAIN','KPI_ADD_FAIL','KPI_UPD_FAIL']))
                        <span>!</span>
                        @else
                        <i class="fa-regular fa-circle-check"></i>
                        @endif
                    </div>

                    <button type="button" class="button top-right" id="hide-message-modal-2">&times;</button>
                </div>

                {{-- Message body --}}
                <div class="text-center body-modal">
                    <p style="font-size:16px;">
                        @switch($msg)
                        @case('KPI_ADDED') KPI added successfully. @break
                        @case('KPI_UPDATED') KPI updated successfully. @break
                        @case('KPI_ADD_FAIL') Failed to add KPI, try again. @break
                        @case('KPI_UPD_FAIL') Failed to update KPI, try again. @break
                        @case('PLEASE_TRY_AGAIN') Something went wrong, please try again. @break
                        @default Operation completed.
                        @endswitch
                    </p>
                    <button class="button rounded-5" id="hide-message-modal">
                        <span>OK</span> <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>

    @endif



    <script>
        //  displaying modal 
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('MODAL_MESSAGE').value != '') {
                var MODAL_DATA = document.getElementById('MODAL_MESSAGE').value;

                var modal = document.getElementById('MESSAGE_MODAL');

                if (MODAL_DATA) {
                    // Show Bootstrap modal
                    // var bootstrapModal = new bootstrap.Modal(modal);
                    // bootstrapModal.show();
                    // document.getElementById('MODAL_MESSAGE').value = '';

                    document.querySelector('#MESSAGE_MODAL').style.display = 'block';
                    setTimeout(() => {
                        document.querySelector('#MESSAGE_MODAL').style.transform = 'translateY(0)';
                        // document.querySelector('.modal-backdrop').remove();
                    }, 10);
                } else {
                    // var bootstrapModal = new bootstrap.Modal(modal);
                    // bootstrapModal.hide();
                }
            }
        });
    </script>

    <script>
        // hiding message modal 
        document.querySelector('#hide-message-modal').addEventListener('click', function() {
            document.querySelector('#MESSAGE_MODAL').style.transform = 'translateY(100%)';
            setTimeout(() => {
                document.querySelector('#MESSAGE_MODAL').style.display = 'none';
                document.body.style.overflow = "scroll";
                document.body.style.padding = "0px";
                document.body.classList.remove("modal-open-2");
                // document.querySelector('.modal-backdrop').remove();
            }, 100);
        });

        document.querySelector('#hide-message-modal-2').addEventListener('click', function() {
            document.querySelector('#MESSAGE_MODAL').style.transform = 'translateY(100%)';
            setTimeout(() => {
                document.querySelector('#MESSAGE_MODAL').style.display = 'none';
                document.body.style.overflow = "scroll";
                document.body.style.padding = "0px";
                document.body.classList.remove("modal-open-2");
                // document.querySelector('.modal-backdrop').remove();
            }, 100);
        });
    </script>
</div>