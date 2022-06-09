#!/usr/bin/env bash
BASEDIR=`dirname $0`; cd $BASEDIR
workdir=$(pwd -P)
export ANSIBLE_CONFIG="$workdir/ansible/ansible.cfg"
VENV="$workdir/ansible/.venv"

_venvactivate() {
  pip install virtualenv
  virtualenv $VENV
  source  $VENV/bin/activate
}

prepare() {
  _venvactivate
  cp -R sql/update.sql  ansible/roles/mysql-loader/files/
  pip install --upgrade pip
  pip install -Ur ansible/requirements/pip.txt
}

_run-playbook-php() {
  ansible-playbook -v -b "$workdir/ansible/deploy.yml" \
  -i $workdir/ansible/inventory/$ZONE \
  --vault-password-file $workdir/ansible/ansible_pass.txt \
    $@  
}
deploy() {
   prepare
   echo "install php code on server  "
   _run-playbook-php  "$@"
}

case "$1" in
  deploy-php)  shift; deploy "$@" ;;
  *) print_help; exit 1
esac
