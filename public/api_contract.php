<?php
// File: public/api_contract.php

$contracts = [
  [
    'Endpoint' => '/api/v1/health',
    'Method' => 'GET',
    'ParamsBody' => '-',
    'Response' => '{ "status":"ok", "time":"2025-11-10T09:00:00+08:00" }'
  ],
  [
    'Endpoint' => '/api/v1/version',
    'Method' => 'GET',
    'ParamsBody' => '-',
    'Response' => '{ "version":"1.0.0" }'
  ],
  [
    'Endpoint' => '/api/v1/auth/login',
    'Method' => 'POST',
    'ParamsBody' => '{ "email":"admin@example.com", "password":"123456" }',
    'Response' => '{ "token":"<JWT_TOKEN>" }'
  ],
  [
    'Endpoint' => '/api/v1/users',
    'Method' => 'GET',
    'ParamsBody' => '?page=1&per_page=10',
    'Response' => '{ "data":[{...}], "meta":{...} }'
  ],
  [
    'Endpoint' => '/api/v1/users/{id}',
    'Method' => 'GET',
    'ParamsBody' => '-',
    'Response' => '{ "id":1, "name":"Admin", "email":"admin@gmail.com" }'
  ],
  [
    'Endpoint' => '/api/v1/users',
    'Method' => 'POST',
    'ParamsBody' => '{ "name":"sarlina", "email":"yurintangmmati@gmail.com", "password":"123456", "role":"user" }',
    'Response' => '{ "success":true, "message":"User berhasil ditambahkan" }'
  ],
  [
    'Endpoint' => '/api/v1/users/{id}',
    'Method' => 'PUT',
    'ParamsBody' => '{ "name":"sarlina Update", "email":"urin toding tangmati@gmail.com", "role":"admin" }',
    'Response' => '{ "success":true, "message":"User berhasil diupdate" }'
  ],
  [
    'Endpoint' => '/api/v1/users/{id}',
    'Method' => 'DELETE',
    'ParamsBody' => '-',
    'Response' => '{ "deleted":true }'
  ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Contract API v1 - PHP Native</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #486f95ff;
      color: #333;
      margin: 0;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #0c0f0fff;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #eaea67ff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    th {
      background: #4bc9c7ff;
      color: white;
      text-align: left;
      padding: 12px;
      font-size: 15px;
    }

    td {
      border-bottom: 1px solid #b87878ff;
      padding: 10px;
      vertical-align: top;
    }

    tr:hover {
      background-color: #f1e8ff;
    }

    pre {
      margin: 0;
      white-space: pre-wrap;
      word-wrap: break-word;
      font-size: 14px;
      background: #f8f8f8;
      border-radius: 5px;
      padding: 6px;
    }

    p {
      text-align: center;
      margin-top: 25px;
      font-size: 14px;
      color: #555;
    }

    /* Responsif */
    @media (max-width: 768px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }

      th {
        position: absolute;
        top: -9999px;
        left: -9999px;
      }

      td {
        border: none;
        position: relative;
        padding-left: 50%;
      }

      td::before {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 45%;
        padding-right: 10px;
        white-space: nowrap;
        font-weight: bold;
        color: #4A148C;
      }

      td:nth-of-type(1)::before { content: "Endpoint"; }
      td:nth-of-type(2)::before { content: "Method"; }
      td:nth-of-type(3)::before { content: "Params/Body"; }
      td:nth-of-type(4)::before { content: "Respon"; }
    }
  </style>
</head>
<body>
  <h2>Contract API v1 - PHP Native</h2>
  <table>
    <thead>
      <tr>
        <th>Endpoint</th>
        <th>Method</th>
        <th>Params/Body</th>
        <th>Respon</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($contracts as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c['Endpoint']) ?></td>
        <td><?= htmlspecialchars($c['Method']) ?></td>
        <td><pre><?= htmlspecialchars($c['ParamsBody']) ?></pre></td>
        <td><pre><?= htmlspecialchars($c['Response']) ?></pre></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p>Versi API: v1.0.0 | Dibuat oleh <strong>sarlina ponno </strong> di Praktikum Pemrograman Jaringan © <?= date('Y') ?></p>
</body>
</html>
