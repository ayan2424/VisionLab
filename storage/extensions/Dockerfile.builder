FROM mcr.microsoft.com/devcontainers/rust:1-bullseye
RUN apt-get update && apt-get install -y curl build-essential git
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs
WORKDIR /workspace
