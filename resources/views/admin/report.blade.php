@extends('admin.layout')

@section('title', 'Admin Report')


@section('content')
    <div class="container-fluid">
        <div class="block-header">
            <h2>Ticket Report</h2>
        </div>

        <div class="row">
            <div
                class="card col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 flex-row justify-content-between align-items-center py-2">
                <div class="wrapper-checkBox-search col-xl-4 col-6 d-flex align-items-center">
                    <div class="checkbox col-2 d-flex align-items-center justify-content-center">
                        <input type="checkbox" id="" style="width: 1.5rem; height: 1.5rem;">
                    </div>
                    <div class="form-group d-flex align-items-center justify-content-center">
                        <div class="form-line">
                            <input type="text" placeholder="Search" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="wrapper-filter-action col-xl-4 col-6 d-flex align-items-center justify-content-evenly">
                    <div class="wrapper-filter">
                        <button class="filter-button dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-submenu dropend">
                                <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Nama</a>
                                <ul class="dropdown-menu">
                                    <li><a class="filter-nama dropdown-item" href="#">Ascending</a></li>
                                    <li><a class="filter-nama dropdown-item" href="#">Descending</a></li>
                                </ul>
                            </li>

                            <li class="dropdown-submenu dropend">
                                <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Add Time
                                    Range</a>
                                <ul class="dropdown-menu">
                                    <input type="date" id="range_datepicker" name="range_datepicker"
                                        class="range_datepicker" placeholder="choose date" readonly />
                                </ul>
                            </li>
                            <li>
                                <a class="dropdown-item" id="show-all" href="#">Show All</a>
                            </li>
                        </ul>
                    </div>
                    <!-- <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Dropdown button
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li class="dropdown-submenu dropend">
                                        <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Nama</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                </ul>
                            </div> -->
                    <!-- <div class="dropdown me-4">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                Filter & Sort
                                            </button>
                                            <ul class="dropdown-menu">

                                                <li class="dropdown-submenu dropend">
                                                    <a class="dropdown-item dropdown-toggle" href="#">Nama</a>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#">Ascending</a></li>
                                                        <li><a class="dropdown-item" href="#">Descending</a></li>
                                                    </ul>
                                                </li>

                                                <li class="dropdown-submenu dropend">
                                                    <a class="dropdown-item dropdown-toggle" href="#">Add Time Range</a>
                                                    <ul class="dropdown-menu p-3">
                                                        <input type="date" id="range_datepicker" name="range_datepicker" class="form-control"
                                                            placeholder="choose date" readonly />
                                                    </ul>
                                                </li>

                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>

                                                <li>
                                                    <a class="dropdown-item" id="show-all" href="#">Show All</a>
                                                </li>
                                            </ul>
                                        </div> -->
                    <div class="action-wrapper col-6 align-items-center d-flex justify-content-around">
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
                <!-- <div class="wrapper-filter">
                                                                                                                                                                                                                                                                                                                                                                                                            <button class="filter-button dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                                                                                                                                                                                                                                                                                                                                                                                                aria-expanded="false">
                                                                                                                                                                                                                                                                                                                                                                                                                Filter
                                                                                                                                                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                                                                                                                                                            <ul class="dropdown-menu">
                                                                                                                                                                                                                                                                                                                                                                                                                <li class="dropdown-submenu dropend">
                                                                                                                                                                                                                                                                                                                                                                                                                    <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Nama</a>
                                                                                                                                                                                                                                                                                                                                                                                                                    <ul class="dropdown-menu">
                                                                                                                                                                                                                                                                                                                                                                                                                        <li><a class="dropdown-item" href="#">Ascending</a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                        <li><a class="dropdown-item" href="#">Descending</a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                    </ul>
                                                                                                                                                                                                                                                                                                                                                                                                                </li>

                                                                                                                                                                                                                                                                                                                                                                                                                <li class="dropdown-submenu dropend">
                                                                                                                                                                                                                                                                                                                                                                                                                    <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Add Time
                                                                                                                                                                                                                                                                                                                                                                                                                        Range</a>
                                                                                                                                                                                                                                                                                                                                                                                                                    <ul class="dropdown-menu">
                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="date" id="range_datepicker" name="range_datepicker" class="range_datepicker"
                                                                                                                                                                                                                                                                                                                                                                                                                            placeholder="choose date" readonly />
                                                                                                                                                                                                                                                                                                                                                                                                                    </ul>
                                                                                                                                                                                                                                                                                                                                                                                                                </li>
                                                                                                                                                                                                                                                                                                                                                                                                                <li>
                                                                                                                                                                                                                                                                                                                                                                                                                    <a class="dropdown-item" id="show-all" href="#">Show All</a>
                                                                                                                                                                                                                                                                                                                                                                                                                </li>
                                                                                                                                                                                                                                                                                                                                                                                                            </ul>
                                                                                                                                                                                                                                                                                                                                                                                                        </div> -->
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