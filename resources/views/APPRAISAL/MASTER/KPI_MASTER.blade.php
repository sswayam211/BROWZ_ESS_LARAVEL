<x-header />


<!-- data table cdn  -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">

<!-- datepicker cdn  -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css" /> -->


<style>
    table {
        overflow: auto !important;
    }

    /* table td,
    table th {
        padding: 10px !important;
    } */

    th {
        background-color: #857e7e !important;
        color: white !important;
    }

    table.dataTable th.dt-type-numeric,
    table.dataTable th.dt-type-date,
    table.dataTable td.dt-type-numeric,
    table.dataTable td.dt-type-date {
        text-align: left;
    }

    table.dataTable th.dt-type-numeric div.dt-column-header,
    table.dataTable th.dt-type-numeric div.dt-column-footer,
    table.dataTable th.dt-type-date div.dt-column-header,
    table.dataTable th.dt-type-date div.dt-column-footer,
    table.dataTable td.dt-type-numeric div.dt-column-header,
    table.dataTable td.dt-type-numeric div.dt-column-footer,
    table.dataTable td.dt-type-date div.dt-column-header,
    table.dataTable td.dt-type-date div.dt-column-footer {
        flex-direction: row;
    }

    .active>.page-link,
    .page-link.active {
        z-index: 3;
        color: var(--bs-pagination-active-color);
        background-color: #857e7e;
        border-color: #857e7e;
    }

    .back {
        color: white;
        background-color: #5e5c5b;
        border: none;
        padding: 5px 12px;
    }

    .steps {
        flex-wrap: wrap;
    }

    .step-circle {
        display: block;
        height: 60px;
        width: 60px;
        border-radius: 50%;
        text-align: center;
        align-content: center;
    }
</style>


<!-- Quill CSS(WORD EDIT TAB) -->
<!-- <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet"> -->

<style>
    #editor-container-1,
    #editor-container-2 {
        height: 300px;
        background: #fff;
        font-size: 15PX;
    }

    .text-sm {
        font-size: 12px;
    }
</style>


<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
<script>
    // filter script 
    document.addEventListener("DOMContentLoaded", function() {
        var statusFilter = document.getElementById("status_filter");

        // Initialize DataTable
        var dataTable;
        if (!$.fn.DataTable.isDataTable('#hr_table')) {
            dataTable = $('#hr_table').DataTable({
                "ordering": true,
                "paging": true
            });
        } else {
            dataTable = $('#hr_table').DataTable();
        }

        function filterTable() {
            var selectedStatus = statusFilter.value.toLowerCase();

            if (selectedStatus === 'all') {
                // Show all data
                dataTable.column(4).search('').draw();
            } else {
                // Filter exact match in column 12 (index 11)
                dataTable.column(4).search('^' + selectedStatus + '$', true, false).draw();
            }
        }

        // By default select "Pending"
        statusFilter.value = 'Pending';
        filterTable();

        // Filter on change
        statusFilter.addEventListener("change", filterTable);
    });
</script>



<div class="p-2 mb-md-0 mb-5 pt-md-1 pb-md-0">

    <div class="panel panel-bd lobidrag mb-5 pb-5 ">
        <div class="d-flex justify-content-start align-items-center flex-wrap ">

            <div class="btn-group" id=" ">
                <a class="back mx-1" href="dashboard.php">

                    <i class="fa fa-angle-double-left" style="font-size:18px"></i>&nbsp;&nbsp;Back </a>
            </div>

            <div class="btn-group" id=" ">
                <a class="button mx-1">

                    <i class="fa fa-list"></i>&nbsp;&nbsp; Appraisal KPI </a>
            </div>

            <div class="btn-group" id=" ">
                <a class="button mx-1 show-modal-2" href="kpi-add-update-form?STATUS=ADD">

                    <i class="fa fa-plus"></i>&nbsp;&nbsp;Add </a>
            </div>

        </div>
        <div class="panel-body" style='min-height:600px;'>
            <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover js-basic-example dataTable" id="hr_table">
                    <thead>
                        <tr class="info">
                            <th style="text-align:center;">Sr.No</th>
                            <th style="text-align:start;">KPI Name</th>
                            <th style="text-align:start;">Status</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($KPI_DATA as $INDEX=>$KPI)

                        @if($KPI->KPI_FRZ_FLAG == 'N')
                        @php
                        $COLOR= 'green';
                        $STATUS= 'Active';
                        @endphp
                        @else
                        @php
                        $COLOR= 'red';
                        $STATUS= 'In-Active';
                        @endphp
                        @endif

                        <tr>
                            <td style="text-align:center;">{{ $INDEX+1 }}</td>
                            <td style="text-align:start;">{{ $KPI->KPI_DESC }}</td>
                            <td class="fw-bold" style="text-align:start; color:{{ $COLOR }}">{{ $STATUS }}</td>
                            <td style="text-align:start; white-space: nowrap;">

                                <a class="btn-sm apply-leave-button show-modal-2" style="display:inline-block;" href="kpi-add-update-form?KPI_SYS_ID={{ $KPI->KPI_SYS_ID }}&STATUS=UPDATE">
                                    <span style="color:green;" class=""><i class="fas fa-edit"></i></span>
                                </a>&nbsp;

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>


            </div>
        </div>
    </div>

</div>


<x-MASTER_MODAL />




<x-message_modal />



<x-footer />