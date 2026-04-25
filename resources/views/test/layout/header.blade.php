
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kuis Pilihan Ganda - Bootstrap 5</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        {{-- MathJax untuk rendering rumus Matematika/Fisika/Kimia --}}
        <script>
            MathJax = {
                tex: { inlineMath: [['\\(', '\\)'], ['$', '$']], displayMath: [['$$', '$$'], ['\\[', '\\]']] },
                svg: { fontCache: 'global' }
            };
        </script>
        <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
        
        <style>
            body { 
                background-color: #f0f2f5; 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            }
            .quiz-card { 
                border: none; 
                border-radius: 20px; 
                box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            }
            
            /* Styling Pilihan Jawaban */
            .option-container { 
                margin-bottom: 12px; 
                position: relative; 
            }
            .btn-check:checked + .option-label {
                background-color: #e7f1ff;
                border-color: #0d6efd;
                color: #0d6efd;
                font-weight: 600;
            }
            .option-label {
                display: flex;
                align-items: center;
                padding: 15px 20px;
                background-color: #fff;
                border: 2px solid #f0f0f0;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.2s ease;
                width: 100%;
            }
            .option-label:hover {
                border-color: #0d6efd;
                background-color: #f8fbff;
                transform: translateY(-2px);
            }
            .option-badge {
                width: 30px;
                height: 30px;
                background-color: #eee;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 15px;
                font-size: 0.9rem;
                font-weight: bold;
            }
            .btn-check:checked + .option-label .option-badge {
                background-color: #0d6efd;
                color: white;
            }

            .btn-success {
                background-color: #28a745;
                border: none;
            }

            .btn-secondary {
                background-color: #6c757d;
                border: none;
            }

            .modal-body a {
                width: 45px;
                text-align: center;
                font-weight: bold;
            }
        </style>
    </head>