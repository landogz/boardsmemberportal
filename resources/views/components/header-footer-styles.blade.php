<style>
    /* Typography Standards */
    
    /* Body Text - Gotham or Montserrat, 14-16px, 1-1.5 line height */
    body, p, span, div, li, td, th, label, input, textarea, select, button {
        font-family: 'Gotham Rounded', 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        font-size: 14px; /* 14px digital (default) */
        line-height: 1.5; /* 1.5 line height for readability */
    }
    
    /* Titles/Headlines - Montserrat Bold (or Gotham Bold fallback), 28-32px, 1.2-1.3 line height */
    h1, .title, .headline {
        font-family: 'Montserrat', 'Gotham Rounded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-weight: 700; /* Bold */
        font-size: 30px; /* 30px digital (middle of 28-32px range) */
        line-height: 1.25; /* 1.25 (middle of 1.2-1.3 range) */
    }
    
    /* Headers/Subheaders - Montserrat Semi-Bold, 20-24px, 1.3 line height */
    h2, h3, h4, h5, h6, .header, .subheader {
        font-family: 'Montserrat', 'Gotham Rounded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-weight: 600; /* Semi-Bold */
        font-size: 22px; /* 22px digital (middle of 20-24px range) */
        line-height: 1.3;
    }
    
    /* Specific heading sizes */
    h2 {
        font-size: 24px;
    }
    
    h3 {
        font-size: 22px;
    }
    
    h4 {
        font-size: 20px;
    }
    
    h5, h6 {
        font-size: 18px;
    }
    
    /* Small text adjustments */
    small, .text-sm, .text-xs {
        font-size: 12px;
        line-height: 1.5;
    }
    
    /* Header and Footer Styles */
    .gov-container {
        max-width: 1190px;
        margin: 0 auto;
        width: 100%;
    }
    
    /* Top Bar - 1190x45px - Mandatory, Locked */
    .top-bar {
        width: 100%;
        height: 45px;
        background-color: #123a60;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 15px;
        font-size: 12px;
        font-family: Arial, Verdana, Tahoma, sans-serif;
        position: -webkit-sticky;
        position: sticky;
    }
    
    .dark .top-bar {
        background-color: #0a1a2e;
    }
    
    /* Search bar styling */
    .search-bar {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .search-bar input {
        padding: 5px 10px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 12px;
        width: 200px;
    }
    
    .search-bar button {
        padding: 5px 15px;
        background-color: #055498;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
        min-height: 32px;
    }
    
    .search-bar button:hover {
        background-color: #123a60;
    }
    
    .dark .search-bar input {
        background-color: #1e293b;
        color: white;
        border-color: #374151;
    }
    
    .dark .search-bar button {
        background-color: #055498;
    }
    
    .dark .search-bar button:hover {
        background-color: #123a60;
    }
    
    /* Agency Footer - 1190 W, H varies - Mandatory, Customizable */
    .agency-footer {
        width: 100%;
        min-height: 200px;
        background-color: #f8f8f8;
        border-top: 2px solid #055498;
        padding: 20px 15px;
    }
    
    .dark .agency-footer {
        background-color: #1e293b;
        border-top-color: #055498;
        color: #F1F5F9;
    }
    
    /* Standard Footer - 1190 W, H varies - Mandatory, Locked */
    .standard-footer {
        width: 100%;
        min-height: 150px;
        background-color: #222222;
        color: #ffffff;
        padding: 20px 15px;
        font-size: 12pt;
        font-family: Arial, sans-serif;
    }
    
    .standard-footer h4 {
        font-family: Arial, sans-serif;
        font-size: 12pt;
        color: #ffffff;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .standard-footer p,
    .standard-footer li,
    .standard-footer a {
        font-family: Arial, sans-serif;
        font-size: 12pt;
        color: #ffffff;
    }
    
    .standard-footer a {
        color: #ffffff;
        text-decoration: none;
    }
    
    .standard-footer a:hover {
        color: #cccccc;
    }
    
    /* Republic Seal in footer - 36x36px with exact margins */
    .standard-footer .republic-seal-container {
        padding: 0;
        margin: 0;
    }
    
    .standard-footer .republic-seal {
        width: 200px;
        height: 200px;
        margin-left: 13px;
        margin-top: 5px;
        margin-bottom: 5px;
        margin-right: 0;
        object-fit: contain;
        display: block;
    }
    
    .dark .standard-footer {
        background-color: #0a0a0a;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .top-bar {
            height: auto;
            min-height: 45px;
            flex-direction: column;
            padding: 8px 15px;
            font-size: 11px;
        }
        
        .search-bar {
            width: 100%;
            margin-top: 8px;
        }
        
        .search-bar input {
            flex: 1;
            width: auto;
        }
        
        .agency-footer,
        .standard-footer {
            padding: 15px 10px;
        }
        
        .standard-footer .republic-seal {
            width: 150px;
            height: 150px;
            margin: 10px auto;
        }
    }
</style>

