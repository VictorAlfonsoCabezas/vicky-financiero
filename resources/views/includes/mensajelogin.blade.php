@if (session("mensaje"))
    <div class="alert alert-dark alert-dismissible alert-sm" data-auto-dismiss="3000">
        <button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">×</button>
        <ul>
            <li>{{ session("mensaje") }}</li>
        </ul>
    </div>
    
@endif