{ pkgs ? import <nixpkgs> { } }:

pkgs.mkShell
{
    nativeBuildInputs = with pkgs; [
        (php83.buildEnv {
            extraConfig = ''
                memory_limit = 6G
                xdebug.mode=coverage
            '';

            extensions = ({ enabled, all }: enabled ++ (with all; [
                xdebug
            ]));
        })
        php83Packages.composer
    ];
}
