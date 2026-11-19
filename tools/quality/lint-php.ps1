$ErrorActionPreference = 'Stop'
$root = Resolve-Path (Join-Path $PSScriptRoot '../..')
Push-Location $root
try {
    $php = (Get-Command php -CommandType Application).Source
    $version = & $php -r 'echo PHP_VERSION;'
    if ($LASTEXITCODE -ne 0) { throw 'No se pudo consultar la version de PHP.' }

    # Include new files before git add.
    $paths = @(git -c core.quotepath=false ls-files --cached --others --exclude-standard -- '*.php')
    if ($LASTEXITCODE -ne 0) { throw 'Git no pudo enumerar los archivos PHP.' }
    $files = @($paths | Sort-Object -Unique | Where-Object { Test-Path -LiteralPath $_ -PathType Leaf })
    if ($files.Count -eq 0) { throw 'No se encontraron archivos PHP.' }

    $failures = 0
    foreach ($file in $files) {
        $output = & $php -l (Join-Path $root $file) 2>&1
        if ($LASTEXITCODE -ne 0) {
            $failures++
            $output | Write-Output
        }
    }
    Write-Output "PHP $version | revisados: $($files.Count) | fallos: $failures"
    if ($failures -gt 0) { exit 1 }
} finally {
    Pop-Location
}
