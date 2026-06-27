{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    pkgs.git
    pkgs.curl
    pkgs.jq
    pkgs.direnv
    pkgs.bash
    pkgs.nano
    pkgs.php
  ];
  
  shellHook = ''
    echo "VisionLab Native Nix Environment Activated"
  '';
}
