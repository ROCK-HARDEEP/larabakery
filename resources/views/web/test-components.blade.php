@extends('web.layouts.app')

@section('content')
<div class="skc-container" style="padding: 40px 15px;">
    <h1 style="font-size: 36px; margin-bottom: 40px; text-align: center;">Component Style Test Page</h1>
    
    <!-- Pagination Test -->
    <section style="margin-bottom: 40px;">
        <h2 style="font-size: 24px; margin-bottom: 20px;">Pagination Component</h2>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <nav class="pagination-wrapper">
                <div class="pagination-info">
                    Showing 1 to 10 of 100 results
                </div>
                <ul class="pagination-list">
                    <li class="pagination-item disabled">
                        <span class="pagination-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                    <li class="pagination-item active">
                        <span class="pagination-link current">1</span>
                    </li>
                    <li class="pagination-item">
                        <a class="pagination-link" href="#">2</a>
                    </li>
                    <li class="pagination-item">
                        <a class="pagination-link" href="#">3</a>
                    </li>
                    <li class="pagination-item">
                        <span class="pagination-ellipsis">...</span>
                    </li>
                    <li class="pagination-item">
                        <a class="pagination-link" href="#">10</a>
                    </li>
                    <li class="pagination-item">
                        <a class="pagination-link" href="#">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
    
    <!-- Buttons Test -->
    <section style="margin-bottom: 40px;">
        <h2 style="font-size: 24px; margin-bottom: 20px;">Button Styles</h2>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; gap: 15px; flex-wrap: wrap;">
            <button class="skc-btn">Primary Button</button>
            <button class="btn-primary">Primary Alt</button>
            <button class="btn-orange">Orange Button</button>
            <button style="padding: 12px 24px; background: white; color: var(--bakery-primary); border: 2px solid var(--bakery-primary); border-radius: 8px; font-weight: 600; cursor: pointer;">Outline Button</button>
        </div>
    </section>
    
    <!-- Badges Test -->
    <section style="margin-bottom: 40px;">
        <h2 style="font-size: 24px; margin-bottom: 20px;">Badge Styles</h2>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; gap: 15px; flex-wrap: wrap;">
            <span class="badge badge-success">Success</span>
            <span class="badge badge-warning">Warning</span>
            <span class="badge badge-danger">Danger</span>
            <span class="badge badge-info">Info</span>
            <span class="status-badge status-success">Active</span>
            <span class="status-badge status-warning">Pending</span>
        </div>
    </section>
    
    <!-- Form Controls Test -->
    <section style="margin-bottom: 40px;">
        <h2 style="font-size: 24px; margin-bottom: 20px;">Form Controls</h2>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Text Input</label>
                <input type="text" placeholder="Enter text here">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Select Dropdown</label>
                <select>
                    <option>Option 1</option>
                    <option>Option 2</option>
                    <option>Option 3</option>
                </select>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Textarea</label>
                <textarea rows="4" placeholder="Enter your message"></textarea>
            </div>
        </div>
    </section>
    
    <!-- Alerts Test -->
    <section style="margin-bottom: 40px;">
        <h2 style="font-size: 24px; margin-bottom: 20px;">Alert Styles</h2>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Success! Your order has been placed.
            </div>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Warning! Stock is running low.
            </div>
            <div class="alert alert-error">
                <i class="fas fa-times-circle"></i>
                Error! Something went wrong.
            </div>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Info! New products added to the catalog.
            </div>
        </div>
    </section>
    
    <!-- Cards Test -->
    <section style="margin-bottom: 40px;">
        <h2 style="font-size: 24px; margin-bottom: 20px;">Card Styles</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div class="card" style="padding: 20px;">
                <h3 style="margin-bottom: 10px;">Card Title</h3>
                <p style="color: var(--gray-600);">This is a sample card component with hover effects.</p>
                <button class="skc-btn" style="margin-top: 15px;">Action</button>
            </div>
            <div class="product-card" style="padding: 20px;">
                <h3 style="margin-bottom: 10px;">Product Card</h3>
                <p style="color: var(--gray-600);">Special styling for product cards.</p>
                <span class="badge badge-success">In Stock</span>
            </div>
        </div>
    </section>
    
    <!-- Table Test -->
    <section style="margin-bottom: 40px;">
        <h2 style="font-size: 24px; margin-bottom: 20px;">Table Styles</h2>
        <div style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Status</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#001</td>
                        <td>Chocolate Cake</td>
                        <td><span class="badge badge-success">Delivered</span></td>
                        <td style="color: var(--bakery-primary); font-weight: 600;">$25.00</td>
                    </tr>
                    <tr>
                        <td>#002</td>
                        <td>Croissant</td>
                        <td><span class="badge badge-warning">Processing</span></td>
                        <td style="color: var(--bakery-primary); font-weight: 600;">$5.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    
    <!-- Loading Spinner Test -->
    <section style="margin-bottom: 40px;">
        <h2 style="font-size: 24px; margin-bottom: 20px;">Loading States</h2>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 20px;">
            <div class="spinner"></div>
            <span>Loading content...</span>
        </div>
    </section>
</div>
@endsection