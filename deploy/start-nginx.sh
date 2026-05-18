while read -r line
do
  export $line
done < ".variables"

docker exec -ti $CONTAINER_NAME bash -c "nginx -g 'daemon on;'"