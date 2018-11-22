            <!-- ========== Left Sidebar Start ========== -->

            <div class="left side-menu">
                <div class="sidebar-inner slimscrollleft">
                    <!--- Divider -->
                    <div id="sidebar-menu">
                        <ul>    
                            @if (auth()->check())
                                @role('Admin')
                                    <li class="menu-title">Main</li>
                                   <!--  <li>
                                        <a href="{{url('/home')}}" class="waves-effect waves-primary"><i
                                            class="ti-home"></i><span> Dashboard </span></a>
                                    </li> -->
                                    <li class="has_sub">
                                        <a href="{{route('products.index')}}" class="waves-effect waves-primary"><i class="fa fa-product-hunt"></i><span> Products </span></a>
                                    </li>
                                    <li class="has_sub">
                                        <a href="{{route('fbt.index')}}" class="waves-effect waves-primary"><i class="fa fa-product-hunt"></i><span> FBT Products</span></a>
                                    </li>
                                    <li class="has_sub">
                                        <a href="{{route('categories.index')}}" class="waves-effect"><i class="mdi mdi-format-list-bulleted"></i><span>Categories</span></a>
                                    </li>
                                    <li class="has_sub">
                                        <a href="{{route('customers.index')}}" class="waves-effect waves-primary"><i class="mdi mdi-account-multiple"></i><span> Customers </span></a>
                                    </li>
                                    <li class="menu-title">Order Management</li>
                                    <li class="has_sub">
                                        <a href="{{route('orders.index')}}" class="waves-effect waves-primary">
                                            <i class="ti-bag"></i>
                                            <span> Orders </span>
                                        </a>
                                        
                                    </li>
                                    <li class="menu-title">Live Shopping Bag / Wishlist</li>
                                    <li class="has_sub">
                                        <a href="{{route('shopping_bags.index')}}" class="waves-effect"><i class="mdi mdi-shopping"></i><span>Shopping Bag</span> </a>
                                    </li>
                                    <li class="has_sub">
                                        <a href="{{route('wishlists.index')}}" class="waves-effect"><i class="mdi mdi-star-circle"></i><span>Wish List</span> </a>
                                    </li>

                                    <li class="menu-title">Discount and Promos</li>

                                    <li class="has_sub">
                                        <a href="{{route('vouchers.index')}}" class="waves-effect waves-primary"><i class="mdi mdi-percent"></i><span> Voucher Codes</span>
                                            <span class="badge badge-primary pull-right">{{@$voucher_active_count}}</span>
                                        </a>
                                    </li>
                                    <li class="has_sub">
                                        <a href="{{route('promos.index')}}" class="waves-effect waves-primary"><i class="ion-android-promotion"></i><span>Promos</span>
                                        </a>
                                    </li>
                                    
                                    <li class="menu-title">Settings</li>
                                    <li class="has_sub">
                                        <a href="{{route('status.index')}}" class="waves-effect waves-primary">
                                            <i class="ti-map"></i><span> Order Status </span>
                                        </a>
                                      
                                        <a href="{{route('stores.index')}}" class="waves-effect waves-primary">
                                            <i class="mdi mdi-store"></i><span> Stores </span>
                                        </a>
                                        <a href="{{route('journals.index')}}" class="waves-effect waves-primary">
                                            <i class="fa fa-newspaper-o"></i><span> Journals </span>
                                        </a>
                                    </li>
                                   <!--  <li class="has_sub">
                                        <a href="{{route('tags.index')}}" class="waves-effect waves-primary">
                                            <i class="fa fa-tags"></i><span> Order Tags </span>
                                        </a>
                                    </li> -->
                                    <li class="has_sub">
                                        <a href="javascript:void(0);" class="waves-effect waves-primary"><i
                                                class="ti-files"></i><span> Pages </span> <span class="menu-arrow"></span></a>
                                        <ul class="list-unstyled">
                                            <li><a href="{{route('pages.banner.index')}}">Banners</a></li>
                                            <li><a href="{{route('pages.return-policy.index')}}">Return Policy</a></li>
                                            <li><a href="{{route('pages.privacy-policy.index')}}">Privacy Policy</a></li>
                                            <li><a href="{{route('pages.terms-condition.index')}}">Terms and Conditions</a></li>
                                            <li><a href="{{route('pages.about.index')}}">About</a></li>
                                            <li><a href="{{route('pages.contact-page.index')}}">Contact Page</a></li>

                                        </ul>
                                    </li>
                                    <li class="has_sub">
                                        <a href="javascript:void(0);" class="waves-effect waves-primary"><i
                                                class="ti-settings"></i><span> Settings </span> <span class="menu-arrow"></span></a>
                                        <ul class="list-unstyled">
                                            <li><a href="{{route('price_range.index')}}">Content Price Range</a></li>
                                            <li><a href="{{route('journals_category.index')}}">Journal Categories</a></li>
                                        </ul>
                                    </li>
                                @else
                                    <li class="menu-title">User Management</li>
                                    <li class="has_sub">
                                        <a href="{{route('users.index')}}" class="waves-effect waves-primary">
                                            <i class="ion-person"></i> <span> Users </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{route('roles.index')}}" class="waves-effect waves-primary">
                                            <i class="ion-ios7-briefcase-outline"></i>
                                            <span> Roles </span>
                                        </a>
                                    </li>

                                    <li class="has_sub">
                                        <a href="{{route('permissions.index')}}" class="waves-effect waves-primary"><i
                                                class="ti-key"></i><span> Permissions </span> 
                                        </a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- Left Sidebar End -->