#!tbin/bash
step=5 #间隔的秒数，不能大于60

for i in `seq 0 $step 55`
do
	curl http://127.0.0.1:8099/api/crontab/warningReminder #调用链接
    if [ "$i" -ne "55" ]; then
    	sleep $step
    fi
done
