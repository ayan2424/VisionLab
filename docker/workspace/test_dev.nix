{pkgs}: {
  channel = "stable-24.11";
  packages = [
    pkgs.sudo
    pkgs.nodejs_22
  ];
  env = {};
  idx = {
    extensions = [
      "google.gemini-cli-vscode-ide-companion"
      "esbenp.prettier-vscode"
    ];
    workspace = {
      onCreate = {
        default.openFiles = [ "README.md" ];
      };
    };
    previews = {
      enable = true;
      previews = {
        web = {
          command = ["php" "artisan" "serve" "--port" "$PORT" "--host" "0.0.0.0"];
          manager = "web";
        };
      };
    };
  };
}
