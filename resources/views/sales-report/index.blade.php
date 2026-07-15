<x-app-layout :title="__('Sales Report')">

    <div class="container-fluid py-4">

        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-sm border">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold mb-0">{{ __('Sales Report') }}</h3>
                        <div class="d-flex gap-2">
                            <button type="button" id="btnPrintReport" class="btn btn-light-primary btn-sm">
                                <i class="fas fa-print me-1"></i> {{ __('Print Report') }}
                            </button>
                            <button type="button" id="btnExportExcel" class="btn btn-light-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i> {{ __('Export to Excel') }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        @include('sales-report.partials.filters')

                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="card bg-light-primary border-0">
                                    <div class="card-body py-4">
                                        <div class="fs-7 fw-bold text-muted mb-1">{{ __('Total Sales Amount') }}</div>
                                        <div class="fs-3 fw-bolder text-primary" id="summaryTotalSales">0.00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light-success border-0">
                                    <div class="card-body py-4">
                                        <div class="fs-7 fw-bold text-muted mb-1">{{ __('Total Paid Amount') }}</div>
                                        <div class="fs-3 fw-bolder text-success" id="summaryTotalPaid">0.00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light-danger border-0">
                                    <div class="card-body py-4">
                                        <div class="fs-7 fw-bold text-muted mb-1">{{ __('Total Due Amount') }}</div>
                                        <div class="fs-3 fw-bolder text-danger" id="summaryTotalDue">0.00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light-warning border-0">
                                    <div class="card-body py-4">
                                        <div class="fs-7 fw-bold text-muted mb-1">{{ __('Total Down Payment') }}</div>
                                        <div class="fs-3 fw-bolder text-warning" id="summaryTotalDownPayment">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="fw-bold fs-6">
                                {{ __('Total Records') }}: <span id="summaryTotalRecords">0</span>
                            </div>
                        </div>

                        @include('sales-report.partials.sales-report-datatable')
                    </div>
                </div>
            </div>

        </div>

    </div>

</x-app-layout>
