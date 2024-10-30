cat <<EOT >> tests/config.yaml
publication: $VMSRPC_PUBLICATION
rpctoken: $VMSRPC_RPCTOKEN
user: $VMSRPC_USER
password: $VMSRPC_PASSWORD
useRPCTokenForUnitTests: $VMSRPC_TOKENS_FOR_TESTS
EOT
