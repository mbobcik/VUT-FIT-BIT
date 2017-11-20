//
// Created by Martin on 18. 3. 2017.
//

#include <iostream>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netdb.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <unistd.h>
#include <sys/stat.h>
#include<fstream>

using namespace std;

enum eCode{
    EOK=0,
    EPARAM,
    EADDR,
    EPORT,
    ESOCK,
    ECON,
    EFOP,
    EFEX, // todo doplnit
    EREQ
};

#define BUFSIZE 1000

// http://localhost:12345/tonda/foo/bar/doc.pdf
// 0123456
struct remoteT{
    string host, remotePath,localPath, command,dirFile;
    int port;
    char* file;
};

struct remoteT resolveURI(string URI){
    string tmp = URI.substr(6);
    int colonPos = tmp.find(":");
    int slashPos = tmp.find("/");
    remoteT result = new remoteT;
    result.host = tmp.substr(0,colonPos);
    result.port = tmp.substr(colonPos,slashPos);
    result.path = tmp.substr(slashPos);

    return result;
}

int execRequest(int &clientSocket, remoteT reqInfo){

    string request;
    int size;
    char buffer[BUFSIZE];

    if(!strcmpi(reqInfo.command,"del")){
        request = "DEL ";
        request.append(reqInfo.remotePath);
        request.append("?type=file HTTP/1.1\n");
        request.append("Date: " << time(0) << "\n");
        request.append("Accept: text/plain\n");
        request.append("Accept-Encoding: UTF-8\n\n");

        if((size = send(clientSocket,request.c_str(),request.size()+1,0)) == -1){
            //todo ret err
        }

        if((size = recv(clientSocket,buffer,BUFSIZE,0)) == -1){
            //todo ret err
        }
        if(buffer.find("200")== string::npos){
            //todo zjistit prichozi chybu
        }
        //todo ret ok

    }else if(!strcmpi(reqInfo.command,"get")){
        request = "GET ";
        request.append(reqInfo.remotePath);
        request.append("?type=file HTTP/1.1\n");
        request.append("Date: " << time(0) << "\n");
        request.append("Accept: application/octet-stream\n");
        request.append("Accept-Encoding: UTF-8\n\n\n");

        if((size = send(clientSocket,request.c_str(),request.size()+1,0)) == -1){
            //todo ret err
        }

        if((size = recv(clientSocket,buffer,BUFSIZE,0)) == -1){
            //todo ret err
        }
        if(buffer.find("200")== string::npos){
         //todo zjistit prichozi chybu
        }

        fstream file;
        file.open(reqInfo.localPath,fstream::out|fstream::binary);
        if(!file.is_open()){
            //todo ret err
        }

        while((size = recv(clientSocket,buffer,BUFSIZE,0))>0){
            file.write(buffer,size);
        }
        if(size==-1){
            unlink(reqInfo.localPath);
            //todo ret err
        }
        file.close();
        //todo ret ok


    }else if(!strcmpi(reqInfo.command,"put")){
        fstream file;
        file.open(reqInfo.localPath,fstream::in|fstream::binary);
        if(!file.is_open()){
            //todo ret err
        }
        request = "PUT ";
        request.append(reqInfo.remotePath);
        request.append("?type=file HTTP/1.1\n");
        request.append("Date: " << time(0) << "\n");
        request.append("Accept: text/plain\n");
        request.append("Accept-Encoding: UTF-8\n");
        request.append("Content-Type: application/octet-stream\n");
        request.append("Content-Length: " << file.gcount() <<"\n\n\n");

        if((size = send(clientSocket,request.c_str(),request.size()+1,0)) == -1){
            //todo ret err
        }

        file.read(buffer, BUFSIZE);
        while ((size = send(clientSocket, buffer, file.gcount(), 0)) > 0) {
            file.read(buffer, BUFSIZE);
        }

        if((size = recv(clientSocket,buffer,BUFSIZE,0)) == -1){
            //todo ret err
        }
        if(size == -1){
            //todo ret err
        }
        file.close();

    }else if(!strcmpi(reqInfo.command,"lst")){
        request = "LST ";
        request.append(reqInfo.remotePath);
        request.append("?type=folder HTTP/1.1\n");
        request.append("Date: " << time(0) << "\n");
        request.append("Accept: text/plain\n");
        request.append("Accept-Encoding: UTF-8\n\n\n");

        if((size = send(clientSocket,request.c_str(),request.size()+1,0)) == -1){
            //todo ret err
        }

        if((size = recv(clientSocket,buffer,BUFSIZE,0)) == -1){
            //todo ret err
        }
        if(buffer.find("200")== string::npos){
            //todo zjistit prichozi chybu
        }

        if((size = recv(clientSocket,buffer,BUFSIZE,0)) == -1){
            //todo ret err
        }
        cout << buffer <<"\n";
        //todo ret ok

    }else if(!strcmpi(reqInfo.command,"mkd")){
        request = "MKD ";
        request.append(reqInfo.remotePath);
        request.append("?type=folder HTTP/1.1\n");
        request.append("Date: " << time(0) << "\n");
        request.append("Accept: text/plain\n");
        request.append("Accept-Encoding: UTF-8\n\n\n");

        if((size = send(clientSocket,request.c_str(),request.size()+1,0)) == -1){
            //todo ret err
        }

        if((size = recv(clientSocket,buffer,BUFSIZE,0)) == -1){
            //todo ret err
        }
        if(buffer.find("200")== string::npos){
            //todo zjistit prichozi chybu
        }
        //todo ret ok

    }else if(!strcmpi(reqInfo.command,"rmd")){
        request = "RMD ";
        request.append(reqInfo.remotePath);
        request.append("?type=folder HTTP/1.1\n");
        request.append("Date: " << time(0) << "\n");
        request.append("Accept: text/plain\n");
        request.append("Accept-Encoding: UTF-8\n\n\n");

        if((size = send(clientSocket,request.c_str(),request.size()+1,0)) == -1){
            //todo ret err
        }
        if((size = recv(clientSocket,buffer,BUFSIZE,0)) == -1){
            //todo ret err
        }
        if(buffer.find("200")== string::npos){
            //todo zjistit prichozi chybu
        }
        //todo ret ok

    }else{
        //todo ret err
    }

    //todo ret ok
}

int main(int argc, char *argv[]) {

//    ftrest COMMAND REMOTE-PATH [LOCAL-PATH]
//    COMMAND je příkaz
//    REMOTE-PATH je cesta k souboru nebo
//                adresáři na serveru
//    LOCAL-PATH je cesta v lokální souborovém systému,
//                povinné pro put

    string command;
    string remotePath;
    string localPath;
    if(argc == 3){ // comm remote
        command = argv[1];
        remotePath = argv[2];
    }else if(args == 4){ // comm remote local
        command = argv[1];
        remotePath = argv[2];
        localPath = argv[3];
    } else{
        //todo ret error
    }

    remoteT URI = resolveURI(remotePath);
    URI.command = command;
    URI.localPath = localPath;
    int clientSocket;
    socklen_t serverLen;
    struct hostent server;
    struct  sockaddr_in serverSocket;

    if((server = gethostbyname(URI.host)) == NULL){
        // todo ret err
    }

    if((clientSocket = socket(AF_INET, SOCK_STREAM, 0)) == -1){
        //todo ret err
    }
    serverSocket.sin_family = AF_INET;
    serverSocket.sin_port = htons(URI.port);
    memcpy(&(serverSocket.sin_addr), server->h_addr, server->h_length);

    if(connect(clientSocket,(struct sockaddr*)&serverSocket, sizeof(serverSocket)) == -1){
        //todo ret err
    }

    //connected, jdem prenaset

    if(execRequest(clientSocket,URI) != 0){
        //todo ret err
    }



    close(clientSocket);
}