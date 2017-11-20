#include <iostream>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netdb.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <unistd.h>
#include <fstream>

using namespace std;

#define BUFSIZE 1000

int execRequest(int &clientSocket){

    int size;
    string msg,command,path;
    char buffer[BUFSIZE];

    if((size = recv(clientSocket,buffer,BUFSIZE,0)) == -1){
        //todo ret err
    }
    msg = buffer;
    command = msg.substr(0,3);
    path = msg.substr(4,msg.find("?")-4);

    if(!strcmpi(command,"DEL")){

        fstream file;
        file.open(path,fstream::in|fstream::binary);
        if (!file.is_open()){
            //todo return error 404
        }
        file.close();
        if(remove(path) != 0){
            //todo ret unknown err
        }

        //todo send OK

    }else if(!strcmpi(command,"GET")){



    }else if(!strcmpi(command,"PUT")){

        fstream file;
        file.open(path,fstream::out|fstream::binary);
        if (!file.is_open()){
            //todo return error 404
        }

        while((size = recv(clientSocket,buffer,BUFSIZE,0)) >0){
            file.write(buffer,size);
        }
        if(size < 0){
            unlink(path);
            //todo ret err
        }

    }else if(!strcmpi(command,"LST")){

    }else if(!strcmpi(command,"MKD")){

    }else if(!strcmpi(command,"RMD")){

    }else{

        //todo send error 400
    }

}

int main(int argc, char *argv[]) {

    //get parameters
    string rootFolder = "";
    int port = 6677;
    char ch;
    while ((ch = getopt(argc, argv, "r:p:")) != -1) {
        switch (ch) {
            case 'r': // root folder
                rootFolder = optarg;
                break;
            case 'p': // port
                port = stoi(optarg);
                break;
            default:
                break;
        }
        argc -= optind;
        argv += optind;
    }

    int serverSocket;
    int clientSocket;
    sockaddr_in clientInfo;
    socklen_t addrLen;
//    Creating a TCP socket, with a call to socket().
    if((serverSocket = socket(AF_INET,SOCK_STREAM,0)) == -1){
        // todo return error
    }
    memset(&clientInfo,0, sizeof clientInfo);

    clientInfo.sin_family = AF_INET;
    clientInfo.sin_port = htons(port);
    clientInfo.sin_addr.s_addr = INADDR_ANY;

    if(bind(serverSocket, (struct sockaddr *)&clientInfo, sizeof clientInfo) == -1){
        //todo return error
    }

    if(listen(serverSocket, 10) == -1){
        // todo ret error
    }

    int childPid;
    while (1) {
        addrLen = sizeof(clientInfo);
        clientSocket = accept(serverSocket, (struct sockaddr *) &clientInfo, &addrLen);

        if (clientSocket > 0) {
            if ((childPid = fork()) < 0) {
                //todo err
            } else if (childPid == 0) { // synator
                close(serverSocket);
                int result;
                if((result = execRequest(clientSocket)) != 0){
                    close(clientSocket);
                    //todo err
                }
                return 0;
            }
            close(clientSocket);
        } else {
            close(serverSocket);
            //todo exit error
        }
    }
}