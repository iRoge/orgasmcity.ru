#!/bin/bash
for pid in $(ps -aux | grep 1c_exchange | grep -v grep | awk -F'[ ]' '{print $2}'); do kill -9 $pid; done
