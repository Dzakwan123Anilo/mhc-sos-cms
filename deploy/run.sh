#!/bin/sh
echo "Step 2/2 : Running container..."

# Load environment variables
while read -r line; do
  export $line
done < ".variables"

IMAGE=$IMAGE_NAME
TAG=$IMAGE_TAG
NAME=$CONTAINER_NAME

# Check if the container exists and remove it
if [ "$(docker ps -aq -f name=$NAME)" ]; then
  docker rm -f $NAME
fi

# Run the Docker container
docker run -tid --restart=always \
--net=host \
-v /etc/timezone:/etc/timezone \
-v /etc/localtime:/etc/localtime \
-v $CMS_SOURCE_PATH:/home/app \
-e DB_HOST=$DB_HOST \
-e DB_PORT=$DB_PORT \
-e DB_USERNAME=$DB_USERNAME \
-e DB_PASSWORD=$DB_PASSWORD \
-e DB_NAME=$DB_NAME \
-e DB_DRIVER=$DB_DRIVER \
--name=$NAME \
$IMAGE:$TAG
