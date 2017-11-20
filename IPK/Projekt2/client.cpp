//
// Created by Martin on 18. 3. 2017.
//

#include <iostream>
#include <sstream>
#include <string>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netdb.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <unistd.h>
#include <sys/stat.h>
#include <stdio.h>
#include <sstream>
#include <openssl/md5.h>
#include <iomanip>

using namespace std;

enum eCode{
    EOK=0,
    EPARAM = 1,
    EADDR = 2,
    EPORT = 3,
    ESOCK = 4,
    ECON = 5,
    EFEX = 6, // todo doplnit
    EREQ = 7
};

#define BUFSIZE 1000

string getCommand(string message){
    if(message[0] == 'S'){
        string result= message.substr(0,4);
        if(strcmp(result.c_str(),"SOLVE")) {
            return "SOLVE";
        }else{
            return "ERROR";
        }
    }else if (message[0] == 'B'){

        string result= message.substr(0,2);
        if(strcmp(result.c_str(),"BYE")) {
            return "BYE";
        }else{
            return "ERROR";
        }
    } else{
        return "ERROR";
        //error
    }
}

int main(int argc, char *argv[]) {

    const char *server_hostname;
    if(argc == 2){ // comm remote
        server_hostname = argv[1];

    } else{
        cerr<<"ERROR: bad params\n";
        return EPARAM;
    }

    int client_socket, port_number, bytestx, bytesrx;
    port_number = 55555;
    struct hostent *server;

    char buf[BUFSIZE];

    bool isIPv4 = true;
    string tmpAddress (server_hostname);
    if(tmpAddress.find(':') != string::npos){
        isIPv4 = false;
    }

    ////////////IPv4///////////////////////
    if(isIPv4) {
        struct sockaddr_in server_address;
        if ((server = gethostbyname(server_hostname)) == NULL) {
            cerr << "ERROR: no such host as " << server_hostname << "\n";
            exit(EADDR);
        }

        /* 3. nalezeni IP adresy serveru a inicializace struktury server_address */
        bzero((char *) &server_address, sizeof(server_address));
        server_address.sin_family = AF_INET;
        bcopy((char *) server->h_addr, (char *) &server_address.sin_addr.s_addr, server->h_length);
        server_address.sin_port = htons(port_number);
        //cout<<"INFO: Server socket: "<< inet_ntoa(server_address.sin_addr)<<" : "<< ntohs(server_address.sin_port)<<"\n";

        //create cocket
        if ((client_socket = socket(AF_INET, SOCK_STREAM, 0)) <= 0) {
            cerr << "ERROR: socket\n";
            exit(ESOCK);
        }
        //connect
        if (connect(client_socket, (const struct sockaddr *) &server_address, sizeof(server_address)) != 0) {
            cerr << "ERROR: connect\n";
            exit(ECON);
        }

    }else{ /////////////////IPv6//////////////////////
        struct sockaddr_in6 server_address;
        if ((server = gethostbyname2(server_hostname, AF_INET6)) == NULL) {
            cerr << "ERROR: no such host as " << server_hostname << "\n";
            exit(EADDR);
        }

        /* 3. nalezeni IP adresy serveru a inicializace struktury server_address */
        bzero((char *) &server_address, sizeof(server_address));
        server_address.sin6_flowinfo = 0; // maybe
        server_address.sin6_family = AF_INET6;
        bcopy((char *) server->h_addr, (char *) &server_address.sin6_addr.s6_addr, server->h_length);
        server_address.sin6_port = htons(port_number);
        //cout<<"INFO: Server socket: "<< inet_ntoa(server_address.sin_addr)<<" : "<< ntohs(server_address.sin_port)<<"\n";

        //create cocket
        if ((client_socket = socket(AF_INET6, SOCK_STREAM, 0)) <= 0) {
            cerr << "ERROR: socket\n";
            exit(ESOCK);
        }
        //connect
        if (connect(client_socket, (const struct sockaddr *) &server_address, sizeof(server_address)) != 0) {
            cerr << "ERROR: connect\n";
            exit(ECON);
        }
    }
    ////////////IP END///////////////////////

    //cout<<"INFO: Connected\n";
    bzero(buf, BUFSIZE);

    unsigned char digest[MD5_DIGEST_LENGTH];
    char login[] = "xbobci00";
    MD5((unsigned char*)&login, strlen(login), (unsigned char*)&digest);
    char mdString[33];
    for(int i = 0; i < 16; i++)
        sprintf(&mdString[i*2], "%02x", (unsigned int)digest[i]);

    //string login = "xbobci00";
    string tmpmd (mdString);
    string message = "HELLO "+ tmpmd + "\n";

    strcpy(buf, message.c_str());

    bytestx = send(client_socket, buf, strlen(buf), 0);
    if (bytestx < 0)
        cerr<<"ERROR in sendto\n";
    //cout<<"INFO: Sent: "<<buf;

    bool loop = true;
    while (loop){

        bzero(buf, BUFSIZE);
        /* prijeti odpovedi a jeji vypsani */
        bytesrx = recv(client_socket, buf, BUFSIZE, 0);
        if (bytesrx < 0)
            cerr<<"ERROR in recvfrom\n";

        //cout<<"INFO: <<Echo from server: "<< buf ;
        string msg(buf);
        string command = getCommand(msg);
        //cout<<"INFO: Command = "<< command<<"\n";

        if(command.find("SOLVE") != std::string::npos){
            //get first number
            int firstSpacePos = msg.find(" ");
            //cout<<"First space pos is "<<firstSpacePos<<"\n";
            int secondSpacePos = msg.find(" ",firstSpacePos + 1);
            //cout<<"Second space pos is "<<secondSpacePos<<"\n";
            string tmp (msg,firstSpacePos+1,secondSpacePos-firstSpacePos-1);
            //cout<<"First stringnumber is \""<<tmp<<"\"\n";
            int firstNumber = atoi(tmp.c_str());
            //cout<<"First number is "<<firstNumber<<"\n";
            //get operation
            firstSpacePos = msg.find(" ",secondSpacePos+1);
            string operation = msg.substr(secondSpacePos+1, firstSpacePos-secondSpacePos-1);
            //cout<<"Operation is \""<<operation<<"\"\n";
            //get second number
            secondSpacePos = msg.find("\n");
            int secondNumber = atoi(msg.substr(firstSpacePos,secondSpacePos-firstSpacePos  ).c_str());
            //cout<<"Second number is "<<secondNumber<<"\n";
            //solve

            if(strlen(operation.c_str())!=1){
                //neznama operace
            }
            string result;
            double resultVal;
            stringstream tmpResult;
            switch (operation[0]){
                case '+':
                    resultVal = firstNumber + secondNumber;
                    tmpResult<<setprecision(3)<<fixed<<resultVal;
                    result = tmpResult.str();
                    result = result.substr(0,strlen(result.c_str())-1);
                    //cout <<"Result is " << result <<"\n";
                    break;
                case '-':
                    resultVal = firstNumber - secondNumber;
                    tmpResult<<setprecision(3)<<fixed<<resultVal;
                    result = tmpResult.str();
                    result = result.substr(0,strlen(result.c_str())-1);
                    //cout <<"Result is " << result <<"\n";
                    break;
                case '*':
                    resultVal = firstNumber * secondNumber;
                    tmpResult<<setprecision(3)<<fixed<<resultVal;
                    result = tmpResult.str();
                    result = result.substr(0,strlen(result.c_str())-1);
                    //cout <<"Result is " << result <<"\n";
                    break;
                case '/':
                    if(secondNumber == 0){
                        result="ERROR";
                    }
                    else {
                        resultVal = (double)firstNumber / (double)secondNumber;
                        tmpResult<<setprecision(3)<<fixed<<resultVal;
                        result = tmpResult.str();
                        result = result.substr(0,strlen(result.c_str())-1);
                    }
                    //cout << "Result is " << result << "\n";
                    break;
                default:
                    //cout<<"Neznama operace, continue...\n";
                    continue;
                    break;
            }

            //send
             message = "RESULT " + result + "\n";
            //string login = "xbobci00";

            strcpy(buf, message.c_str());
            bytestx = send(client_socket, buf, strlen(buf), 0);
            if (bytestx < 0)
                cerr<<"ERROR in sendto\n";
            //cout<<"INFO: >>Sent: "<<buf;

        }else if (command.find( "BYE") != std::string::npos){
            //get secret
            //print secret
            cout<<msg.substr(4,strlen(msg.c_str()) - 4 - 1); // todo good
            // !loop
            loop = false;
        }else{
            //cout<<"INFO: Wrong message, continue...\n";
        }
        if(!loop){
            close(client_socket);
            //cout << "\nINFO: connection closed\n";
            return EOK;
        }
    }
}