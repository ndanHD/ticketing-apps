@extends('admin.layout')

@section('title', 'Admin Report')


@section('content')
    <div class="container-fluid">
        <div class="block-header">
            <h2>Ticket Report</h2>
        </div>

        <div class="row">
            <div class="card col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 flex-row justify-content-evenly">
                <div class="checkbox d-flex align-items-center justify-content-center">
                    <input type="checkbox" id="" style="width: 1.5rem; height: 1.5rem;">
                </div>
                <div class="form-group d-flex align-items-center justify-content-center">
                    <div class="form-line">
                        <input type="text" placeholder="Search" class="form-control">
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Dropdown button
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row clearfix">
            <div class="card col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 flex-row justify-content-evenly">
                <div class="checkbox d-flex align-items-center justify-content-center">
                    <input type="checkbox" id="" style="width: 1.5rem; height: 1.5rem;">
                </div>
                <div class="ticket-sections-wrapper row d-flex justify-content-center g-2">
                    <div class="ticket-section col-3 col-xl-3 py-3 d-flex justify-content-center">
                        <div class="wrapper-ticket-section text-center">
                            <h5>Title Ticket</h5>
                            <span>COntohjudul</span>
                        </div>
                    </div>
                    <div class="ticket-section col-3 col-xl-3 py-3 d-flex justify-content-center">
                        <div class="wrapper-ticket-section text-center">
                            <h5>SLA</h5>
                            <span>COntohjudul</span>
                        </div>
                    </div>
                    <div class="ticket-section col-3 col-xl-3 py-3 d-flex justify-content-center">
                        <div class="wrapper-ticket-section text-center">
                            <h5>Priority</h5>
                            <span>COntohjudul</span>
                        </div>
                    </div>
                    <div class="ticket-section col-3 col-xl-3 py-3 d-flex justify-content-center">
                        <div class="wrapper-ticket-section text-center">
                            <h5>Tanggal Ticket</h5>
                            <span>COntohjudul</span>
                        </div>
                    </div>
                    <div class="ticket-section col-3 col-xl-3 py-3 d-flex justify-content-center">
                        <div class="wrapper-ticket-section text-center">
                            <h5>Type Ticket</h5>
                            <span>COntohjudul</span>
                        </div>
                    </div>
                    <div class="ticket-section col-3 col-xl-3 py-3 d-flex justify-content-center">
                        <div class="wrapper-ticket-section text-center">
                            <h5>Status Ticket</h5>
                            <span>COntohjudul</span>
                        </div>
                    </div>
                    <div class="ticket-section col-3 col-xl-3 py-3 d-flex justify-content-center">
                        <div class="wrapper-ticket-section text-center">
                            <h5>Created By</h5>
                            <span>COntohjudul</span>
                        </div>
                    </div>
                    <div class="ticket-section col-3 col-xl-3 py-3 d-flex justify-content-center">
                        <div class="wrapper-ticket-section text-center">
                            <h5>Rating Ticket</h5>
                            <span>COntohjudul</span>
                        </div>
                    </div>
                </div>
                <div class="action-wrapper col-2 align-items-center d-flex justify-content-around">
                    <div class="wrapper-icon btn btn-info text-white p-2 rounded">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <div class="wrapper-icon btn btn-warning text-white p-2 rounded">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div class="wrapper-icon btn btn-success text-white p-2 rounded">
                        <i class="fa-solid fa-download"></i>
                    </div>
                    <a class="wrapper-icon btn btn-danger text-white p-2 rounded">
                        <i class="fa-solid fa-trash-can"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection